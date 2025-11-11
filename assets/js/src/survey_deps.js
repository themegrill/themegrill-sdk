import formbricks from '@formbricks/js';

/**
 * Check if we can set the user ID.
 *
 * If we set the user without checking, and error will be raised.
 *
 * @see https://github.com/formbricks/formbricks/blob/9fcbe4e8c56c524cec2dcdf6f28482fa2e8779a3/packages/js-core/src/lib/user/user.ts#L18-L27
 *
 * @returns boolean
 */
function canAddUserId() {
	const rawData = localStorage.getItem('formbricks-js');
	if (!rawData) {
		return true;
	}

	try {
		const data = JSON.parse(rawData);
		if (data?.user?.data?.userId) {
			return false;
		}
	} catch (e) {
		console.warn(e);
	}

	return true;
}

/**
 * Load the formbricks library and expose it to the global scope.
 * Emit a custom event to let other scripts know that formbricks is loaded.
 */
document.addEventListener('DOMContentLoaded', () => {
	window.tgsdk_formbricks = {
		init: async (args) => {
			if (typeof args !== 'object' || args === null) {
				args = {};
			}

			const mergedArgs = {
				...window.tgsdk_survey_data,
				...args,
				attributes: {
					...(window.tgsdk_survey_data.attributes ?? {}),
					...(args.attributes ?? {}),
				},
			};

			const { environmentId, appUrl, attributes, userId } = mergedArgs;

			// See https://github.com/formbricks/formbricks/blob/main/packages/js-core/src/index.ts
			await formbricks?.setup({
				environmentId,
				appUrl,
			});
			formbricks?.setAttributes(attributes);
			if (canAddUserId()) {
				formbricks?.setUserId(userId);
			}
		},
	};

	const isNumeric = (value) => !isNaN(value) && typeof value !== 'boolean';

	let timer = null;

	// Auto-trigger if the survey use the new format delivered with SDK.
	if (isNumeric(window.tgsdk_survey_data?.attributes?.install_days_number)) {
		timer = setTimeout(() => {
			window.tgsdk_formbricks?.init();
		}, 350);
	}

	// Cancel auto-trigger if a plugin request manual control.
	window.addEventListener('themegrill:survey:trigger:cancel', () => {
		clearTimeout(timer);
	});

	window.dispatchEvent(new Event('themegrill:survey:loaded'));
});
