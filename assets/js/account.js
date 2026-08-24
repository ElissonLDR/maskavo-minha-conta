/**
 * Minha Conta — tabs, edição de perfil, senha.
 */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', fn);
		} else {
			fn();
		}
	}

	function cfg() {
		return window.maskavoMc || {};
	}

	function setFeedback(el, message, ok) {
		if (!el) return;
		el.hidden = !message;
		el.textContent = message || '';
		el.classList.toggle('is-ok', !!ok);
		el.classList.toggle('is-err', !ok && !!message);
	}

	function isMobileNav() {
		return window.matchMedia('(max-width: 860px)').matches;
	}

	function activateTab(root, key) {
		root.querySelectorAll('.maskavo-mc-nav [data-mc-tab]').forEach(function (btn) {
			var on = btn.getAttribute('data-mc-tab') === key;
			btn.classList.toggle('is-active', on);
			btn.setAttribute('aria-selected', on ? 'true' : 'false');
		});
		root.querySelectorAll('[data-mc-panel]').forEach(function (panel) {
			var on = panel.getAttribute('data-mc-panel') === key;
			panel.classList.toggle('is-active', on);
			if (on) {
				panel.removeAttribute('hidden');
			} else {
				panel.setAttribute('hidden', '');
			}
		});
	}

	function showMobileHome(root) {
		root.classList.remove('is-mobile-section');
		var home = root.querySelector('[data-mc-mobile-home]');
		var back = root.querySelector('[data-mc-back]');
		if (home) home.hidden = false;
		if (back) back.hidden = true;
		root.querySelectorAll('[data-mc-panel]').forEach(function (panel) {
			panel.classList.remove('is-active');
			panel.setAttribute('hidden', '');
		});
	}

	function openMobileSection(root, key) {
		activateTab(root, key);
		root.classList.add('is-mobile-section');
		var home = root.querySelector('[data-mc-mobile-home]');
		var back = root.querySelector('[data-mc-back]');
		if (home) home.hidden = true;
		if (back) back.hidden = false;
		try {
			root.scrollIntoView({ behavior: 'smooth', block: 'start' });
		} catch (e) {
			/* ignore */
		}
	}

	function syncViewportMode(root) {
		if (isMobileNav()) {
			if (!root.classList.contains('is-mobile-section')) {
				showMobileHome(root);
			}
		} else {
			root.classList.remove('is-mobile-section');
			var home = root.querySelector('[data-mc-mobile-home]');
			var back = root.querySelector('[data-mc-back]');
			if (home) home.hidden = true;
			if (back) back.hidden = true;
			var active = root.querySelector('.maskavo-mc-nav [data-mc-tab].is-active');
			var key = active ? active.getAttribute('data-mc-tab') : null;
			if (!key) {
				var first = root.querySelector('.maskavo-mc-nav [data-mc-tab]');
				key = first ? first.getAttribute('data-mc-tab') : null;
			}
			if (key) activateTab(root, key);
		}
	}

	function post(action, data) {
		var c = cfg();
		var body = new FormData();
		body.append('action', action);
		body.append('nonce', c.nonce || '');
		Object.keys(data || {}).forEach(function (k) {
			body.append(k, data[k]);
		});
		return fetch(c.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			credentials: 'same-origin',
			body: body,
		}).then(function (r) {
			return r.json();
		});
	}

	function initRoot(root) {
		/* Evita :focus do Elementor/tema grudado após clique */
		root.addEventListener('click', function (e) {
			var t = e.target;
			if (!t || !t.closest) return;
			var btn = t.closest('button, .maskavo-mc-btn');
			if (btn && root.contains(btn) && typeof btn.blur === 'function') {
				window.setTimeout(function () {
					btn.blur();
				}, 0);
			}
		});

		root.querySelectorAll('.maskavo-mc-nav [data-mc-tab]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				activateTab(root, btn.getAttribute('data-mc-tab'));
			});
		});

		root.querySelectorAll('[data-mc-open-section]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				openMobileSection(root, btn.getAttribute('data-mc-open-section'));
			});
		});

		var backBtn = root.querySelector('[data-mc-back]');
		if (backBtn) {
			backBtn.addEventListener('click', function () {
				showMobileHome(root);
			});
		}

		syncViewportMode(root);
		window.addEventListener('resize', function () {
			syncViewportMode(root);
		});

		var card = root.querySelector('[data-mc-profile-card]');
		if (card) {
			var feedback = card.querySelector('[data-mc-feedback]');

			function applyProfile(p) {
				if (!p) return;
				var nameEl = card.querySelector('[data-mc-display-name]');
				var firstEl = card.querySelector('[data-mc-first-name]');
				var lastEl = card.querySelector('[data-mc-last-name]');
				var navName = root.querySelector('.maskavo-mc-nav__name');
				var mobileName = root.querySelector('.maskavo-mc-mobile-home__name');
				if (nameEl && p.display_name) nameEl.textContent = p.display_name;
				if (navName && p.display_name) navName.textContent = p.display_name;
				if (mobileName && p.display_name) mobileName.textContent = p.display_name;
				if (firstEl) firstEl.textContent = p.first_name || '—';
				if (lastEl) lastEl.textContent = p.last_name || '—';
			}

			function validateField(key, value) {
				var i18n = cfg().i18n || {};
				var v = String(value || '').trim();
				if (!v) {
					return i18n.nameRequired || 'Preencha o campo antes de salvar.';
				}
				if (key === 'first_name') {
					if (/\d/.test(v)) {
						return i18n.firstNameNoNum || 'O nome não pode conter números.';
					}
					if (/\s/.test(v)) {
						return i18n.firstNameOneWord || 'O nome deve ter apenas uma palavra.';
					}
					if (!/^[\p{L}'’\-]+$/u.test(v)) {
						return i18n.firstNameOneWord || 'O nome deve ter apenas uma palavra.';
					}
				}
				if (key === 'last_name') {
					if (/\d/.test(v)) {
						return i18n.lastNameNoNum || 'O sobrenome não pode conter números.';
					}
					if (!/^[\p{L}'’\-\s]+$/u.test(v)) {
						return i18n.lastNameNoNum || 'O sobrenome não pode conter números.';
					}
				}
				return '';
			}

			function closeAllFields() {
				card.querySelectorAll('[data-mc-field]').forEach(function (field) {
					if (field.getAttribute('data-mc-locked') === '1') return;
					var view = field.querySelector('.maskavo-mc-field__view');
					var wrap = field.querySelector('.maskavo-mc-field__edit-wrap');
					var editBtn = field.querySelector('[data-mc-field-edit]');
					if (view) view.hidden = false;
					if (wrap) wrap.hidden = true;
					if (editBtn) editBtn.hidden = false;
					field.classList.remove('is-editing');
				});
			}

			function openField(field) {
				closeAllFields();
				var view = field.querySelector('.maskavo-mc-field__view');
				var wrap = field.querySelector('.maskavo-mc-field__edit-wrap');
				var editBtn = field.querySelector('[data-mc-field-edit]');
				var input = field.querySelector('.maskavo-mc-field__input');
				var valueEl = field.querySelector('.maskavo-mc-field__view .maskavo-mc-field__value');
				if (view) view.hidden = true;
				if (wrap) wrap.hidden = false;
				if (editBtn) editBtn.hidden = true;
				field.classList.add('is-editing');
				if (input && valueEl) {
					var current = valueEl.textContent.trim();
					if (current === '—') current = '';
					input.value = current;
					input.focus();
					input.select();
				}
			}

			card.querySelectorAll('[data-mc-field-edit]').forEach(function (btn) {
				btn.addEventListener('click', function () {
					var field = btn.closest('[data-mc-field]');
					if (field) openField(field);
				});
			});

			card.querySelectorAll('.maskavo-mc-field__input').forEach(function (input) {
				input.addEventListener('keydown', function (e) {
					if (e.key === 'Enter') {
						e.preventDefault();
						var field = input.closest('[data-mc-field]');
						var save = field && field.querySelector('[data-mc-field-save]');
						if (save) save.click();
					}
					if (e.key === 'Escape') {
						closeAllFields();
						setFeedback(feedback, '', true);
					}
				});
			});

			card.querySelectorAll('[data-mc-field-cancel]').forEach(function (btn) {
				btn.addEventListener('click', function () {
					closeAllFields();
					setFeedback(feedback, '', true);
				});
			});

			card.querySelectorAll('[data-mc-field-save]').forEach(function (btn) {
				btn.addEventListener('click', function () {
					var field = btn.closest('[data-mc-field]');
					if (!field) return;
					var key = field.getAttribute('data-mc-field');
					var input = field.querySelector('.maskavo-mc-field__input');
					if (!key || !input) return;
					var i18n = cfg().i18n || {};
					var value = String(input.value || '').trim();
					if (key === 'last_name') {
						value = value.replace(/\s+/g, ' ');
						input.value = value;
					}
					var err = validateField(key, value);
					if (err) {
						setFeedback(feedback, err, false);
						input.focus();
						return;
					}
					var payload = {};
					payload[key] = value;
					btn.disabled = true;
					setFeedback(feedback, i18n.saving || 'Salvando…', true);
					post('maskavo_mc_update_profile', payload)
						.then(function (json) {
							if (!json || !json.success) {
								setFeedback(
									feedback,
									(json && json.data && json.data.message) || i18n.error || 'Erro',
									false
								);
								return;
							}
							var p = (json.data && json.data.profile) || {};
							applyProfile(p);
							card.querySelectorAll('.maskavo-mc-field__input').forEach(function (inp) {
								if (inp.name && Object.prototype.hasOwnProperty.call(p, inp.name)) {
									inp.value = p[inp.name] || '';
								}
							});
							setFeedback(feedback, (json.data && json.data.message) || i18n.saved || 'Salvo!', true);
							closeAllFields();
						})
						.catch(function () {
							setFeedback(feedback, i18n.error || 'Erro', false);
						})
						.finally(function () {
							btn.disabled = false;
						});
				});
			});

			/* Tooltip atenção (e-mail) */
			card.querySelectorAll('[data-mc-tooltip]').forEach(function (tip) {
				var trigger = tip.querySelector('button');
				var bubble = tip.querySelector('.maskavo-mc-tooltip__bubble');
				if (!trigger || !bubble) return;

				function closeTip() {
					bubble.hidden = true;
					trigger.setAttribute('aria-expanded', 'false');
					tip.classList.remove('is-open');
				}

				function openTip() {
					card.querySelectorAll('[data-mc-tooltip].is-open').forEach(function (other) {
						if (other !== tip) {
							var b = other.querySelector('.maskavo-mc-tooltip__bubble');
							var t = other.querySelector('button');
							if (b) b.hidden = true;
							if (t) t.setAttribute('aria-expanded', 'false');
							other.classList.remove('is-open');
						}
					});
					bubble.hidden = false;
					trigger.setAttribute('aria-expanded', 'true');
					tip.classList.add('is-open');
				}

				trigger.addEventListener('click', function (e) {
					e.stopPropagation();
					if (bubble.hidden) openTip();
					else closeTip();
				});

				trigger.addEventListener('mouseenter', function () {
					openTip();
				});
				tip.addEventListener('mouseleave', function () {
					closeTip();
				});
			});

			document.addEventListener('click', function (e) {
				if (!e.target.closest || e.target.closest('[data-mc-tooltip]')) return;
				card.querySelectorAll('[data-mc-tooltip].is-open').forEach(function (tip) {
					var bubble = tip.querySelector('.maskavo-mc-tooltip__bubble');
					var trigger = tip.querySelector('button');
					if (bubble) bubble.hidden = true;
					if (trigger) trigger.setAttribute('aria-expanded', 'false');
					tip.classList.remove('is-open');
				});
			});

			/* Avatar upload / remove */
			var avatarWrap = card.querySelector('[data-mc-avatar]');
			var avatarInput = card.querySelector('[data-mc-avatar-input]');
			var avatarImg = card.querySelector('[data-mc-avatar-img]');
			var avatarFb = card.querySelector('[data-mc-avatar-feedback]');
			var removeBtn = card.querySelector('[data-mc-avatar-remove]');
			var pickBtns = card.querySelectorAll('[data-mc-avatar-pick]');

			function applyAvatarProfile(p) {
				if (!p) return;
				if (avatarImg) {
					var url = p.avatar_url || cfg().defaultAvatarUrl || '';
					if (!p.has_avatar && cfg().defaultAvatarUrl) {
						url = cfg().defaultAvatarUrl;
					}
					if (url) {
						avatarImg.src = url + (url.indexOf('?') >= 0 ? '&' : '?') + 't=' + Date.now();
					}
				}
				if (removeBtn) {
					removeBtn.hidden = !p.has_avatar;
					if (!p.has_avatar) {
						removeBtn.setAttribute('hidden', '');
					} else {
						removeBtn.removeAttribute('hidden');
					}
				}
			}

			pickBtns.forEach(function (btn) {
				btn.addEventListener('click', function () {
					if (avatarInput) avatarInput.click();
				});
			});

			if (avatarInput) {
				avatarInput.addEventListener('change', function () {
					var file = avatarInput.files && avatarInput.files[0];
					if (!file) return;
					var i18n = cfg().i18n || {};
					if (file.size > 2 * 1024 * 1024) {
						setFeedback(avatarFb, i18n.avatarLarge || 'Máximo 2 MB.', false);
						avatarInput.value = '';
						return;
					}
					if (avatarWrap) avatarWrap.classList.add('is-busy');
					setFeedback(avatarFb, i18n.uploading || 'Enviando…', true);
					var body = new FormData();
					body.append('action', 'maskavo_mc_update_avatar');
					body.append('nonce', cfg().nonce || '');
					body.append('avatar', file);
					fetch(cfg().ajaxUrl || '/wp-admin/admin-ajax.php', {
						method: 'POST',
						credentials: 'same-origin',
						body: body,
					})
						.then(function (r) {
							return r.json();
						})
						.then(function (json) {
							if (!json || !json.success) {
								setFeedback(
									avatarFb,
									(json && json.data && json.data.message) || i18n.error || 'Erro',
									false
								);
								return;
							}
							applyAvatarProfile((json.data && json.data.profile) || {});
							setFeedback(avatarFb, (json.data && json.data.message) || i18n.avatarOk || 'Foto atualizada.', true);
						})
						.catch(function () {
							setFeedback(avatarFb, i18n.error || 'Erro', false);
						})
						.finally(function () {
							if (avatarWrap) avatarWrap.classList.remove('is-busy');
							avatarInput.value = '';
						});
				});
			}

			if (removeBtn) {
				removeBtn.addEventListener('click', function () {
					var i18n = cfg().i18n || {};
					if (avatarWrap) avatarWrap.classList.add('is-busy');
					post('maskavo_mc_remove_avatar', {})
						.then(function (json) {
							if (!json || !json.success) {
								setFeedback(
									avatarFb,
									(json && json.data && json.data.message) || i18n.error || 'Erro',
									false
								);
								return;
							}
							applyAvatarProfile((json.data && json.data.profile) || {});
							setFeedback(avatarFb, (json.data && json.data.message) || i18n.avatarRemoved || 'Foto removida.', true);
						})
						.catch(function () {
							setFeedback(avatarFb, i18n.error || 'Erro', false);
						})
						.finally(function () {
							if (avatarWrap) avatarWrap.classList.remove('is-busy');
						});
				});
			}
		}

		var pwForm = root.querySelector('[data-mc-password-form]');
		if (pwForm) {
			pwForm.addEventListener('submit', function (e) {
				e.preventDefault();
				var feedback = pwForm.querySelector('[data-mc-feedback]');
				var btn = pwForm.querySelector('button[type="submit"]');
				var i18n = cfg().i18n || {};
				if (btn) {
					btn.disabled = true;
					btn.textContent = i18n.saving || 'Salvando…';
				}
				post('maskavo_mc_change_password', {
					current_password: (pwForm.current_password && pwForm.current_password.value) || '',
					new_password: (pwForm.new_password && pwForm.new_password.value) || '',
					confirm_password: (pwForm.confirm_password && pwForm.confirm_password.value) || '',
				})
					.then(function (json) {
						if (!json || !json.success) {
							setFeedback(
								feedback,
								(json && json.data && json.data.message) || i18n.error || 'Erro',
								false
							);
							return;
						}
						setFeedback(feedback, (json.data && json.data.message) || i18n.saved || 'Salvo!', true);
						pwForm.reset();
					})
					.catch(function () {
						setFeedback(feedback, i18n.error || 'Erro', false);
					})
					.finally(function () {
						if (btn) {
							btn.disabled = false;
							btn.textContent = 'Atualizar senha';
						}
					});
			});
		}
	}

	ready(function () {
		document.querySelectorAll('[data-maskavo-mc]').forEach(initRoot);
	});
})();
