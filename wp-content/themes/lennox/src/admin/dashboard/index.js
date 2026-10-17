import {
	Button,
	Card,
	CardBody,
	ExternalLink,
	Icon,
	Modal,
	Notice,
	SelectControl,
	TextControl,
	Tooltip,
	ToggleControl,
} from '@wordpress/components';
import {
	comment,
	color,
	envelope,
	external,
	footer,
	header,
	layout,
	pages,
	styles,
	typography,
} from '@wordpress/icons';
import {
	createRoot,
	Fragment,
	render,
	useEffect,
	useMemo,
	useRef,
	useState,
} from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

const ICON_MAP = {
	styles,
	color,
	typography,
	layout,
	header,
	footer,
	comment,
	envelope,
	pages,
};

const CARD_STYLE = {
	border: '1px solid var(--border-color)',
	boxShadow: 'none',
};

const AVAILABLE_LABEL = __('Yes', 'lennox');
const UNAVAILABLE_LABEL = __('No', 'lennox');

const dashboardSettings = window.lennoxThemeDashboard || {};
const rootElement = document.getElementById('lennox-theme-dashboard-root');

const isEnabledState = (value) => true === value || 'true' === value;

const getInitialModuleStates = (modules = []) =>
	modules.reduce((states, module) => {
		if (module?.slug) {
			states[module.slug] = isEnabledState(module.enabled);
		}

		return states;
	}, {});

const getInitialModuleSettings = (modules = []) =>
	modules.reduce((settingsMap, module) => {
		if (
			module?.slug &&
			module?.settings &&
			'object' === typeof module.settings
		) {
			settingsMap[module.slug] = {
				...module.settings,
			};
		}

		return settingsMap;
	}, {});

const MODULE_EDIT_OPTIONS =
	dashboardSettings.moduleEditOptions &&
	'object' === typeof dashboardSettings.moduleEditOptions
		? dashboardSettings.moduleEditOptions
		: {};

const SUPPORTED_MODULE_FIELD_TYPES = [
	'select',
	'text',
	'url',
	'email',
	'tel',
	'separator',
];

const getModuleFields = (moduleConfig = {}) => {
	if (!moduleConfig || 'object' !== typeof moduleConfig) {
		return [];
	}

	if (Array.isArray(moduleConfig.fields)) {
		return moduleConfig.fields.filter(
			(field) =>
				field &&
				'object' === typeof field &&
				field.key &&
				SUPPORTED_MODULE_FIELD_TYPES.includes(field.type || 'select')
		);
	}

	if (moduleConfig.settingKey) {
		return [
			{
				key: moduleConfig.settingKey,
				type: moduleConfig.type || 'select',
				label: moduleConfig.label,
				options: moduleConfig.options,
				default: moduleConfig.default,
			},
		].filter((field) =>
			SUPPORTED_MODULE_FIELD_TYPES.includes(field.type || 'select')
		);
	}

	return [];
};

const saveModuleState = async (moduleSlug, enabled) => {
	if (!dashboardSettings.ajaxUrl || !dashboardSettings.ajaxNonce) {
		throw new Error('Dashboard AJAX settings are unavailable.');
	}

	const requestBody = new window.URLSearchParams({
		action: 'lennox_save_module_state',
		nonce: dashboardSettings.ajaxNonce,
		module: moduleSlug,
		enabled: enabled ? 'true' : 'false',
	});

	const response = await window.fetch(dashboardSettings.ajaxUrl, {
		method: 'POST',
		credentials: 'same-origin',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
		},
		body: requestBody.toString(),
	});

	const result = await response.json();

	if (!response.ok || !result?.success) {
		throw new Error(
			result?.data?.message || 'Unable to save module state.'
		);
	}

	return result;
};

const saveModuleSettings = async (moduleSlug, settings) => {
	if (!dashboardSettings.ajaxUrl || !dashboardSettings.ajaxNonce) {
		throw new Error('Dashboard AJAX settings are unavailable.');
	}

	const requestBody = new window.URLSearchParams({
		action: 'lennox_save_module_settings',
		nonce: dashboardSettings.ajaxNonce,
		module: moduleSlug,
		settings: JSON.stringify(settings),
	});

	const response = await window.fetch(dashboardSettings.ajaxUrl, {
		method: 'POST',
		credentials: 'same-origin',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
		},
		body: requestBody.toString(),
	});

	const result = await response.json();

	if (!response.ok || !result?.success) {
		throw new Error(
			result?.data?.message || 'Unable to save module settings.'
		);
	}

	return result;
};

const importStarterSite = async (starterSlug) => {
	if (!dashboardSettings.ajaxUrl || !dashboardSettings.ajaxNonce) {
		throw new Error('Dashboard AJAX settings are unavailable.');
	}

	const requestBody = new window.URLSearchParams({
		action: 'lennox_import_starter_site',
		nonce: dashboardSettings.ajaxNonce,
		starter: starterSlug,
	});

	const response = await window.fetch(dashboardSettings.ajaxUrl, {
		method: 'POST',
		credentials: 'same-origin',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
		},
		body: requestBody.toString(),
	});

	const result = await response.json();

	if (!response.ok || !result?.success) {
		throw new Error(
			result?.data?.message || 'Unable to import starter site.'
		);
	}

	return result;
};

const getTabFromUrl = () => {
	const url = new window.URL(window.location.href);

	return (
		url.searchParams.get('tab') || dashboardSettings.activeTab || 'welcome'
	);
};

const isAllowedTab = (tabName, tabs) =>
	tabs.some((tab) => tab.name === tabName);

const updateTabInUrl = (tabName) => {
	const url = new window.URL(window.location.href);

	url.searchParams.set('tab', tabName);
	window.history.pushState({ tab: tabName }, '', url);
};

const getQuickLinkCards = (links = []) =>
	links.reduce((cards, link) => {
		cards.push(link);

		if (Array.isArray(link.children) && link.children.length) {
			cards.push(...link.children);
		}

		return cards;
	}, []);

function ResourceCard({ resource }) {
	const hasQuickLinks =
		Array.isArray(resource.links) && resource.links.length;
	const quickLinkCards = hasQuickLinks
		? getQuickLinkCards(resource.links)
		: [];

	return (
		<div
			className={
				hasQuickLinks
					? 'lennox-theme-dashboard__resource-card lennox-theme-dashboard__resource-card--quick-links'
					: 'lennox-theme-dashboard__resource-card'
			}
		>
			{hasQuickLinks ? (
				<div className="lennox-theme-dashboard__quick-links-section">
					<div className="lennox-theme-dashboard__quick-links-header">
						<div>
							<h3 className="lennox-theme-dashboard__card-title">
								{resource.title}
							</h3>
							{resource.description ? (
								<p className="lennox-theme-dashboard__card-description">
									{resource.description}
								</p>
							) : null}
						</div>
						{resource.url && resource.actionLabel ? (
							<ExternalLink href={resource.url}>
								{resource.actionLabel}
							</ExternalLink>
						) : null}
					</div>
					<div className="lennox-theme-dashboard__quick-links-grid">
						{quickLinkCards.map((link) => {
							const isProLocked = Boolean(link.isProLocked);
							const linkClassName = isProLocked
								? 'lennox-theme-dashboard__quick-link-card-link lennox-theme-dashboard__quick-link-card-link--disabled'
								: 'lennox-theme-dashboard__quick-link-card-link';

							return (
								<Card
									key={link.url}
									className={
										isProLocked
											? 'lennox-theme-dashboard__quick-link-card lennox-theme-dashboard__quick-link-card--pro-locked'
											: 'lennox-theme-dashboard__quick-link-card'
									}
									isRounded={false}
									style={CARD_STYLE}
								>
									<CardBody
										className="lennox-theme-dashboard__quick-link-card-body"
										style={{ padding: '20px 24px' }}
									>
										<a
											className={linkClassName}
											href={link.url}
											aria-disabled={
												isProLocked ? 'true' : undefined
											}
											tabIndex={
												isProLocked ? -1 : undefined
											}
										>
											<span
												className="lennox-theme-dashboard__quick-link-icon"
												aria-hidden="true"
											>
												{link.icon &&
												ICON_MAP[link.icon] ? (
													<Icon
														icon={
															ICON_MAP[link.icon]
														}
													/>
												) : (
													link.label.charAt(0)
												)}
											</span>
											<span className="lennox-theme-dashboard__quick-link-content">
												<span className="lennox-theme-dashboard__quick-link-title-row">
													<span className="lennox-theme-dashboard__quick-link-title">
														{link.label}
													</span>
													{isProLocked ? (
														<span className="lennox-theme-dashboard__pro-badge">
															{__(
																'Pro',
																'lennox'
															)}
														</span>
													) : null}
													{!isProLocked ? (
														<span
															className="lennox-theme-dashboard__quick-link-external-icon"
															aria-hidden="true"
														>
															<Icon
																icon={external}
															/>
														</span>
													) : null}
												</span>
												{link.description ? (
													<span className="lennox-theme-dashboard__quick-link-description">
														{link.description}
													</span>
												) : null}
											</span>
										</a>
									</CardBody>
								</Card>
							);
						})}
					</div>
				</div>
			) : (
				<Fragment>
					<h3 className="lennox-theme-dashboard__card-title">
						{resource.title}
					</h3>
					<p className="lennox-theme-dashboard__card-description">
						{resource.description}
					</p>
					<ExternalLink href={resource.url}>
						{resource.title}
					</ExternalLink>
				</Fragment>
			)}
		</div>
	);
}

function ModulesSection({ section, isProActive, upgradeUrl, onNavigate }) {
	const modules = useMemo(
		() => (Array.isArray(section.modules) ? section.modules : []),
		[section.modules]
	);
	const [moduleStates, setModuleStates] = useState(() =>
		getInitialModuleStates(modules)
	);
	const [moduleSettings, setModuleSettings] = useState(() =>
		getInitialModuleSettings(modules)
	);
	const [pendingModules, setPendingModules] = useState({});
	const [editingModuleSlug, setEditingModuleSlug] = useState('');
	const [editingValues, setEditingValues] = useState({});
	const [isSavingSettings, setIsSavingSettings] = useState(false);
	const [settingsError, setSettingsError] = useState('');

	useEffect(() => {
		setModuleStates(getInitialModuleStates(modules));
		setModuleSettings(getInitialModuleSettings(modules));
	}, [modules]);

	const getDefaultEditValues = (moduleSlug) => {
		const moduleConfig = MODULE_EDIT_OPTIONS[moduleSlug];
		const moduleFields = getModuleFields(moduleConfig);
		const nextValues = {};

		if (!moduleFields.length) {
			return nextValues;
		}

		moduleFields.forEach((field) => {
			const currentValue = moduleSettings?.[moduleSlug]?.[field.key];

			if (field.type === 'select' && Array.isArray(field.options)) {
				const optionValues = field.options.map(
					(option) => option.value
				);
				if (optionValues.includes(currentValue)) {
					nextValues[field.key] = currentValue;
					return;
				}

				nextValues[field.key] =
					field.default || field.options[0]?.value || '';
				return;
			}

			nextValues[field.key] =
				'string' === typeof currentValue
					? currentValue
					: field.default || '';
		});

		return nextValues;
	};

	if (!modules.length) {
		return null;
	}

	const handleModuleToggle = async (moduleSlug, nextEnabled) => {
		if (!moduleSlug) {
			return;
		}

		setModuleStates((currentState) => ({
			...currentState,
			[moduleSlug]: nextEnabled,
		}));
		setPendingModules((currentState) => ({
			...currentState,
			[moduleSlug]: true,
		}));

		try {
			await saveModuleState(moduleSlug, nextEnabled);
		} catch (error) {
			setModuleStates((currentState) => ({
				...currentState,
				[moduleSlug]: !nextEnabled,
			}));
		} finally {
			setPendingModules((currentState) => ({
				...currentState,
				[moduleSlug]: false,
			}));
		}
	};

	const handleOpenEditModal = (moduleSlug) => {
		const moduleConfig = MODULE_EDIT_OPTIONS[moduleSlug];
		const moduleFields = getModuleFields(moduleConfig);

		if (!moduleFields.length) {
			return;
		}

		setEditingModuleSlug(moduleSlug);
		setEditingValues(getDefaultEditValues(moduleSlug));
		setSettingsError('');
	};

	const handleCloseEditModal = () => {
		if (isSavingSettings) {
			return;
		}

		setEditingModuleSlug('');
		setEditingValues({});
		setSettingsError('');
	};

	const handleSaveModuleSettings = async () => {
		const moduleConfig = MODULE_EDIT_OPTIONS[editingModuleSlug];
		const moduleFields = getModuleFields(moduleConfig);

		if (!editingModuleSlug || !moduleFields.length) {
			return;
		}

		const nextSettings = moduleFields.reduce((settings, field) => {
			settings[field.key] =
				'string' === typeof editingValues[field.key]
					? editingValues[field.key]
					: '';
			return settings;
		}, {});

		setIsSavingSettings(true);
		setSettingsError('');

		try {
			const result = await saveModuleSettings(
				editingModuleSlug,
				nextSettings
			);
			const savedSettings =
				result?.data?.settings &&
				'object' === typeof result.data.settings
					? result.data.settings
					: nextSettings;

			setModuleSettings((currentState) => ({
				...currentState,
				[editingModuleSlug]: {
					...(currentState[editingModuleSlug] || {}),
					...savedSettings,
				},
			}));
			handleCloseEditModal();
		} catch (error) {
			setSettingsError(
				error?.message ||
					__('Unable to save module settings.', 'lennox')
			);
		} finally {
			setIsSavingSettings(false);
		}
	};

	const editingModule =
		modules.find((module) => module.slug === editingModuleSlug) || null;
	const editingConfig = editingModuleSlug
		? MODULE_EDIT_OPTIONS[editingModuleSlug]
		: null;
	const editingFields = getModuleFields(editingConfig);
	const renderModuleAction = (
		module,
		isLink,
		isPending,
		isEnabled,
		isProLocked
	) => {
		if (isLink) {
			return (
				<Button
					variant="secondary"
					onClick={() => onNavigate(module.tab || module.slug)}
				>
					{__('Open', 'lennox')}
				</Button>
			);
		}

		const toggleControl = (
			<div
				className="lennox-theme-dashboard__module-card-toggle"
				style={{
					cursor: isProLocked ? 'not-allowed' : 'pointer',
				}}
			>
				<ToggleControl
					label=""
					checked={isEnabled}
					disabled={isProLocked || isPending}
					onChange={(nextState) =>
						handleModuleToggle(module.slug, nextState)
					}
					__nextHasNoMarginBottom
				/>
			</div>
		);

		if (!isProLocked) {
			return toggleControl;
		}

		return (
			<Tooltip
				text={__(
					'This module is available only in Lennox Pro.',
					'lennox'
				)}
			>
				<span className="lennox-theme-dashboard__module-card-toggle-target">
					{toggleControl}
				</span>
			</Tooltip>
		);
	};

	return (
		<div className="lennox-theme-dashboard__resource-card lennox-theme-dashboard__resource-card--quick-links">
			<div className="lennox-theme-dashboard__quick-links-section">
				<div className="lennox-theme-dashboard__quick-links-header">
					<div>
						<h3 className="lennox-theme-dashboard__card-title">
							{isProActive
								? section.title
								: __('Build Better with Modules', 'lennox')}
						</h3>
					</div>
					{!isProActive && upgradeUrl ? (
						<ExternalLink
							className="lennox-theme-dashboard__upgrade-link"
							href={upgradeUrl}
						>
							{__('Upgrade to Pro', 'lennox')}
						</ExternalLink>
					) : null}
				</div>
				<div className="lennox-theme-dashboard__quick-links-grid">
					{modules.map((module) => {
						const isProLocked =
							Boolean(module.isPro) && !isProActive;
						const isLink = Boolean(module.isLink);
						const isPending = Boolean(pendingModules[module.slug]);
						const isEnabled = Boolean(moduleStates[module.slug]);
						const moduleConfig = MODULE_EDIT_OPTIONS[module.slug];
						const moduleFields = getModuleFields(moduleConfig);
						const canEdit =
							isEnabled &&
							!isProLocked &&
							Boolean(moduleConfig) &&
							moduleFields.length > 0;

						return (
							<Card
								key={module.slug}
								className={[
									'lennox-theme-dashboard__quick-link-card',
									'lennox-theme-dashboard__module-card',
									isProLocked
										? 'lennox-theme-dashboard__quick-link-card--pro-locked'
										: '',
								]
									.filter(Boolean)
									.join(' ')}
								isRounded={false}
								style={CARD_STYLE}
							>
								<CardBody
									className="lennox-theme-dashboard__quick-link-card-body"
									style={{ padding: '20px 24px' }}
								>
									<div className="lennox-theme-dashboard__quick-link-card-inner lennox-theme-dashboard__module-card-inner">
										<div className="lennox-theme-dashboard__quick-link-content lennox-theme-dashboard__module-card-content">
											<span className="lennox-theme-dashboard__quick-link-title-row">
												<span className="lennox-theme-dashboard__quick-link-title">
													{module.title}
												</span>
											</span>
											{module.description ? (
												<span className="lennox-theme-dashboard__quick-link-description">
													{module.description}
												</span>
											) : null}
											<div className="lennox-theme-dashboard__module-card-actions">
												{canEdit ? (
													<Button
														className="lennox-theme-dashboard__module-card-edit"
														variant="link"
														onClick={() =>
															handleOpenEditModal(
																module.slug
															)
														}
														disabled={isPending}
													>
														{__('Edit', 'lennox')}
													</Button>
												) : null}
												{renderModuleAction(
													module,
													isLink,
													isPending,
													isEnabled,
													isProLocked
												)}
											</div>
										</div>
									</div>
								</CardBody>
							</Card>
						);
					})}
				</div>
			</div>
			{editingModule && editingConfig ? (
				<Modal
					className="lennox-theme-dashboard__module-settings-modal"
					title={`${__('Edit', 'lennox')}: ${editingModule.title}`}
					onRequestClose={handleCloseEditModal}
				>
					{settingsError ? (
						<Notice status="error" isDismissible={false}>
							{settingsError}
						</Notice>
					) : null}
					<div className="lennox-theme-dashboard__module-settings-fields">
						{editingFields.map((field) => {
							if (field.type === 'separator') {
								return (
									<hr
										key={field.key}
										style={{
											margin: '12px 0',
											border: 0,
											borderTop: '1px solid #dcdcde',
										}}
									/>
								);
							}

							if (field.type === 'select') {
								return (
									<SelectControl
										key={field.key}
										label={field.label}
										value={editingValues[field.key] || ''}
										options={field.options || []}
										onChange={(nextValue) =>
											setEditingValues(
												(currentValues) => ({
													...currentValues,
													[field.key]: nextValue,
												})
											)
										}
									/>
								);
							}

							return (
								<TextControl
									key={field.key}
									type={field.type || 'text'}
									label={field.label}
									value={editingValues[field.key] || ''}
									onChange={(nextValue) =>
										setEditingValues((currentValues) => ({
											...currentValues,
											[field.key]: nextValue,
										}))
									}
								/>
							);
						})}
					</div>
					<div
						className="lennox-theme-dashboard__module-settings-actions"
						style={{
							display: 'flex',
							justifyContent: 'flex-end',
							gap: '8px',
						}}
					>
						<Button
							variant="tertiary"
							onClick={handleCloseEditModal}
							disabled={isSavingSettings}
						>
							{__('Cancel', 'lennox')}
						</Button>
						<Button
							variant="primary"
							onClick={handleSaveModuleSettings}
							isBusy={isSavingSettings}
							disabled={isSavingSettings}
						>
							{__('Save', 'lennox')}
						</Button>
					</div>
				</Modal>
			) : null}
		</div>
	);
}

function WelcomePanel({
	section,
	modulesSection,
	isProActive,
	upgradeUrl,
	productName,
	version,
	onNavigate,
}) {
	const resources = Array.isArray(section.resources) ? section.resources : [];
	const firstResource = resources[0] || null;
	const remainingResources = resources.slice(1);
	const starterSite = section.starterSite || null;

	return (
		<Fragment>
			<div className="lennox-theme-dashboard__resource-grid">
				{starterSite ? (
					<Card
						className="lennox-theme-dashboard__starter-sites-banner"
						isRounded={false}
						style={CARD_STYLE}
					>
						<CardBody
							className="lennox-theme-dashboard__starter-sites-banner-body"
							style={{ padding: '28px 24px' }}
						>
							<div>
								<h1 className="lennox-theme-dashboard__card-title">
									{productName
										? sprintf(
												/* translators: %s: Theme name. */
												__('Welcome to %s', 'lennox'),
												productName
											)
										: starterSite.title}
									{version ? (
										<span className="lennox-theme-dashboard__version-inline lennox-theme-dashboard__version-inline--sup">
											{sprintf(
												/* translators: %s: Theme version number. */
												__('v%s', 'lennox'),
												version
											)}
										</span>
									) : null}
								</h1>
								{starterSite.description ? (
									<p className="lennox-theme-dashboard__card-description">
										{starterSite.description}
									</p>
								) : null}
							</div>
							<Button
								variant="primary"
								onClick={() =>
									onNavigate(
										starterSite.tab || 'starter-sites'
									)
								}
							>
								{starterSite.buttonLabel ||
									__('Browse Starter Sites', 'lennox')}
							</Button>
						</CardBody>
					</Card>
				) : null}
				{firstResource ? (
					<ResourceCard
						key={firstResource.url || firstResource.title}
						resource={firstResource}
					/>
				) : null}
				<ModulesSection
					section={modulesSection}
					isProActive={isProActive}
					upgradeUrl={upgradeUrl}
					onNavigate={onNavigate}
				/>
				{remainingResources.map((resource) => (
					<ResourceCard
						key={resource.url || resource.title}
						resource={resource}
					/>
				))}
			</div>
		</Fragment>
	);
}

function StarterSitesPanel({ section, isProActive }) {
	const starters = Array.isArray(section.starters) ? section.starters : [];
	const upgradeUrl = section.upgradeUrl || '';
	const [pendingStarter, setPendingStarter] = useState('');
	const [notice, setNotice] = useState(null);

	const handleImport = async (starterSlug) => {
		if (!starterSlug || pendingStarter) {
			return;
		}

		setPendingStarter(starterSlug);
		setNotice(null);

		try {
			const result = await importStarterSite(starterSlug);
			setNotice({
				status: 'success',
				message:
					result?.data?.message ||
					__('Starter site imported.', 'lennox'),
				siteUrl: result?.data?.siteUrl || '',
			});
		} catch (error) {
			setNotice({
				status: 'error',
				message:
					error?.message ||
					__('Unable to import starter site.', 'lennox'),
			});
		} finally {
			setPendingStarter('');
		}
	};

	return (
		<Fragment>
			{notice ? (
				<Notice status={notice.status} isDismissible={false}>
					{notice.message}
					{notice.siteUrl ? (
						<ExternalLink
							className="lennox-theme-dashboard__starter-site-notice-link"
							href={notice.siteUrl}
						>
							{__('View site', 'lennox')}
						</ExternalLink>
					) : null}
				</Notice>
			) : null}
			<div className="lennox-theme-dashboard__starter-sites-grid">
				{starters.map((starter) => {
					const isPending = pendingStarter === starter.slug;
					const isProLocked = Boolean(starter.isPro) && !isProActive;

					return (
						<Card
							key={starter.slug}
							className="lennox-theme-dashboard__quick-link-card lennox-theme-dashboard__starter-site-card"
							isRounded={false}
							style={CARD_STYLE}
						>
							<CardBody
								className="lennox-theme-dashboard__quick-link-card-body"
								style={{ padding: '20px 24px' }}
							>
								{starter.screenshot ? (
									<div className="lennox-theme-dashboard__starter-site-image-wrap">
										<img
											className="lennox-theme-dashboard__starter-site-image"
											src={starter.screenshot}
											alt=""
											loading="lazy"
										/>
										{starter.isPro ? (
											<span className="lennox-theme-dashboard__pro-badge lennox-theme-dashboard__starter-site-pro-badge">
												{__('Pro', 'lennox')}
											</span>
										) : null}
									</div>
								) : null}
								<div className="lennox-theme-dashboard__starter-site-footer">
									<div className="lennox-theme-dashboard__quick-link-content">
										<span className="lennox-theme-dashboard__quick-link-title-row">
											<span className="lennox-theme-dashboard__quick-link-title">
												{starter.title}
											</span>
										</span>
									</div>
									<div className="lennox-theme-dashboard__starter-site-actions">
										{starter.preview ? (
											<Button
												variant="secondary"
												href={starter.preview}
												target="_blank"
												rel="noopener noreferrer"
											>
												{__('Preview', 'lennox')}
											</Button>
										) : null}
										{isProLocked ? (
											<Button
												variant="primary"
												href={upgradeUrl}
											>
												{__('Upgrade Now', 'lennox')}
											</Button>
										) : (
											<Button
												variant="primary"
												onClick={() =>
													handleImport(starter.slug)
												}
												isBusy={isPending}
												disabled={Boolean(
													pendingStarter
												)}
											>
												{isPending
													? __('Importing', 'lennox')
													: __('Import', 'lennox')}
											</Button>
										)}
									</div>
								</div>
							</CardBody>
						</Card>
					);
				})}
			</div>
			{!starters.length ? (
				<Notice status="warning" isDismissible={false}>
					{__('No bundled starter sites are available.', 'lennox')}
				</Notice>
			) : null}
		</Fragment>
	);
}

function FreeVsProPanel({ section }) {
	const renderAvailabilityCell = (value) => {
		if (value === AVAILABLE_LABEL) {
			return (
				<span
					className="lennox-theme-dashboard__availability-icon lennox-theme-dashboard__availability-icon--available"
					aria-label={AVAILABLE_LABEL}
				>
					<svg
						width="17"
						height="12"
						viewBox="0 0 17 12"
						fill="none"
						xmlns="http://www.w3.org/2000/svg"
						aria-hidden="true"
					>
						<path
							d="M16 1L6 11L1 6"
							stroke="#65A961"
							strokeWidth="2"
							strokeLinecap="round"
							strokeLinejoin="round"
						/>
					</svg>
				</span>
			);
		}

		if (value === UNAVAILABLE_LABEL) {
			return (
				<span
					className="lennox-theme-dashboard__availability-icon lennox-theme-dashboard__availability-icon--unavailable"
					aria-label={UNAVAILABLE_LABEL}
				>
					<svg
						width="13"
						height="12"
						viewBox="0 0 13 12"
						fill="none"
						xmlns="http://www.w3.org/2000/svg"
						aria-hidden="true"
					>
						<path
							fillRule="evenodd"
							clipRule="evenodd"
							d="M11.7193 1.69471C11.9014 1.50611 12.0022 1.25351 12 0.991311C11.9977 0.729114 11.8925 0.478302 11.7071 0.292894C11.5217 0.107485 11.2709 0.00231622 11.0087 3.78025e-05C10.7465 -0.00224062 10.4939 0.0985542 10.3053 0.280712L6.01229 4.57371L1.71929 0.280712C1.53069 0.0985542 1.27808 -0.00224062 1.01589 3.78025e-05C0.75369 0.00231622 0.502878 0.107485 0.31747 0.292894C0.132062 0.478302 0.0268924 0.729114 0.0246139 0.991311C0.0223355 1.25351 0.12313 1.50611 0.305288 1.69471L4.59829 5.98771L0.305288 10.2807C0.209778 10.373 0.133596 10.4833 0.0811869 10.6053C0.0287779 10.7273 0.00119157 10.8585 3.77571e-05 10.9913C-0.00111606 11.1241 0.0241854 11.2558 0.0744663 11.3787C0.124747 11.5016 0.199 11.6132 0.292893 11.7071C0.386786 11.801 0.498438 11.8753 0.621334 11.9255C0.74423 11.9758 0.87591 12.0011 1.00869 12C1.14147 11.9988 1.27269 11.9712 1.39469 11.9188C1.5167 11.8664 1.62704 11.7902 1.71929 11.6947L6.01229 7.40171L10.3053 11.6947C10.3975 11.7902 10.5079 11.8664 10.6299 11.9188C10.7519 11.9712 10.8831 11.9988 11.0159 12C11.1487 12.0011 11.2803 11.9758 11.4032 11.9255C11.5261 11.8753 11.6378 11.801 11.7317 11.7071C11.8256 11.6132 11.8998 11.5016 11.9501 11.3787C12.0004 11.2558 12.0257 11.1241 12.0245 10.9913C12.0234 10.8585 11.9958 10.7273 11.9434 10.6053C11.891 10.4833 11.8148 10.373 11.7193 10.2807L7.42629 5.98771L11.7193 1.69471Z"
							fill="#EC8B8B"
						/>
					</svg>
				</span>
			);
		}

		return value;
	};

	return (
		<Fragment>
			<div className="lennox-theme-dashboard__comparison-table-wrap">
				<table className="lennox-theme-dashboard__comparison-table">
					<thead>
						<tr>
							<th scope="col">{__('Feature', 'lennox')}</th>
							<th scope="col">{__('Free', 'lennox')}</th>
							<th scope="col">{__('Pro', 'lennox')}</th>
						</tr>
					</thead>
					<tbody>
						{(section.comparison || []).map((row) => (
							<tr key={row.feature}>
								<th scope="row">{row.feature}</th>
								<td>{renderAvailabilityCell(row.free)}</td>
								<td>{renderAvailabilityCell(row.pro)}</td>
							</tr>
						))}
					</tbody>
					<tfoot className="lennox-theme-dashboard__comparison-table-footer">
						<tr>
							<td colSpan={3}>
								<Button
									variant="primary"
									href={section.buttonUrl}
									target="_blank"
								>
									{section.buttonLabel}
								</Button>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</Fragment>
	);
}

function DashboardApp() {
	const tabs = useMemo(() => dashboardSettings.tabs || [], []);
	const tabListRef = useRef(null);
	const tabRefs = useRef({});
	const modulesSection = dashboardSettings.modulesSection || {};
	const isProActive = Boolean(dashboardSettings.isProActive);
	const upgradeUrl = dashboardSettings.upgradeUrl || '';
	const version = dashboardSettings.version || '';
	const themeName = dashboardSettings.heading || __('Lennox', 'lennox');
	const themeInitial = themeName.trim().charAt(0).toUpperCase() || 'F';
	const productName = useMemo(() => {
		return isProActive ? `${themeName} ${__('Pro', 'lennox')}` : themeName;
	}, [isProActive, themeName]);
	const getSectionForTab = (tabName) => {
		if (tabName === 'free-vs-pro') {
			return dashboardSettings.freeVsPro || {};
		}

		if (tabName === 'starter-sites') {
			return dashboardSettings.starterSites || {};
		}

		return dashboardSettings.welcome || {};
	};
	const renderPanel = (tab) => {
		const section = getSectionForTab(tab.name);

		if (tab.name === 'free-vs-pro') {
			return (
				<div className="lennox-theme-dashboard__panel">
					<div className="lennox-theme-dashboard__panel-body">
						<FreeVsProPanel section={section} />
					</div>
				</div>
			);
		}

		if (tab.name === 'starter-sites') {
			return (
				<div className="lennox-theme-dashboard__panel">
					<div className="lennox-theme-dashboard__panel-body">
						<StarterSitesPanel
							section={section}
							isProActive={isProActive}
						/>
					</div>
				</div>
			);
		}

		return (
			<div className="lennox-theme-dashboard__panel">
				<div className="lennox-theme-dashboard__panel-body">
					<WelcomePanel
						section={section}
						modulesSection={modulesSection}
						isProActive={isProActive}
						upgradeUrl={upgradeUrl}
						productName={productName}
						version={version}
						onNavigate={handleTabSelect}
					/>
				</div>
			</div>
		);
	};
	const renderBranding = () => {
		return (
			<div className="lennox-theme-dashboard__branding">
				<span
					className="lennox-theme-dashboard__brand-logo"
					aria-hidden="true"
				>
					{themeInitial}
				</span>
				<div className="lennox-theme-dashboard__brand-content">
					<h2 className="lennox-theme-dashboard__brand-name">
						{themeName}
					</h2>
				</div>
			</div>
		);
	};

	const [activeTab, setActiveTab] = useState(() => {
		const initialTab = getTabFromUrl();

		return isAllowedTab(initialTab, tabs) ? initialTab : 'welcome';
	});
	const [tabIndicatorStyle, setTabIndicatorStyle] = useState(null);
	const activeTabData = tabs.find((tab) => tab.name === activeTab) || tabs[0];
	const handleTabSelect = (tabName) => {
		if (!tabName || tabName === activeTab) {
			return;
		}

		setActiveTab(tabName);
		updateTabInUrl(tabName);
	};

	useEffect(() => {
		const handlePopState = () => {
			const nextTab = getTabFromUrl();

			setActiveTab(isAllowedTab(nextTab, tabs) ? nextTab : 'welcome');
		};

		window.addEventListener('popstate', handlePopState);

		return () => {
			window.removeEventListener('popstate', handlePopState);
		};
	}, [tabs]);

	useEffect(() => {
		const updateTabIndicator = () => {
			const activeTabElement = tabRefs.current[activeTab];
			const tabListElement = tabListRef.current;

			if (!activeTabElement || !tabListElement) {
				return;
			}

			const activeTabRect = activeTabElement.getBoundingClientRect();
			const tabListRect = tabListElement.getBoundingClientRect();

			setTabIndicatorStyle({
				left: activeTabRect.left - tabListRect.left,
				width: activeTabRect.width,
			});
		};
		const animationFrameId =
			window.requestAnimationFrame(updateTabIndicator);
		const resizeObserver = window.ResizeObserver
			? new window.ResizeObserver(updateTabIndicator)
			: null;

		window.addEventListener('resize', updateTabIndicator);
		resizeObserver?.observe(tabListRef.current);

		return () => {
			window.cancelAnimationFrame(animationFrameId);
			window.removeEventListener('resize', updateTabIndicator);
			resizeObserver?.disconnect();
		};
	}, [activeTab]);

	if (!tabs.length) {
		return (
			<Notice status="warning" isDismissible={false}>
				{__('Dashboard data is unavailable.', 'lennox')}
			</Notice>
		);
	}

	return (
		<div className="lennox-theme-dashboard__app">
			<div className="lennox-theme-dashboard__tabs-shell">
				<div className="lennox-theme-dashboard__tabs">
					<div
						ref={tabListRef}
						className={`components-tab-panel__tabs${
							tabIndicatorStyle ? ' has-tab-indicator' : ''
						}`}
						role="tablist"
						aria-label={__('Dashboard sections', 'lennox')}
					>
						{renderBranding()}
						{tabs.map((tab) => (
							<Button
								key={tab.name}
								ref={(element) => {
									tabRefs.current[tab.name] = element;
								}}
								className={`components-button components-tab-panel__tabs-item${
									activeTab === tab.name ? ' is-active' : ''
								}`}
								onClick={() => handleTabSelect(tab.name)}
								role="tab"
								aria-selected={activeTab === tab.name}
							>
								{tab.title}
							</Button>
						))}
						{tabIndicatorStyle ? (
							<span
								className="lennox-theme-dashboard__tab-indicator"
								style={tabIndicatorStyle}
								aria-hidden="true"
							/>
						) : null}
						<div className="lennox-theme-dashboard__version-actions">
							<ExternalLink
								className="lennox-theme-dashboard__upgrade-link"
								href={upgradeUrl}
							>
								{__('Upgrade to Pro', 'lennox')}
							</ExternalLink>
						</div>
					</div>
					{activeTabData ? renderPanel(activeTabData) : null}
				</div>
			</div>
		</div>
	);
}

if (rootElement) {
	if (typeof createRoot === 'function') {
		createRoot(rootElement).render(<DashboardApp />);
	} else {
		render(<DashboardApp />, rootElement);
	}
}
