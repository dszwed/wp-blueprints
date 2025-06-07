import {
    Copy,
    Download,
    ExternalLink,
    Loader2,
    Plus,
    Save,
    Search,
    Trash2,
} from 'lucide-react';
import { useState } from 'react';
import { router } from '@inertiajs/react';
import ErrorHandler from './ErrorHandler';
import InputError from './InputError';

interface Plugin {
    slug: string;
    name: string;
    short_description: string;
    version: string;
}

interface Blueprint {
    name: string;
    landingPage: string;
    preferredVersions: {
        php: string;
        wp: string;
    };
    features: {
        networking: boolean;
    };
    steps: {
        step: string;
        username?: string;
        password?: string;
        plugin?: string;
    }[];
}

const phpVersions = ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0'];
const wordpressVersions = [
    '6.8',
    '6.7',
    '6.6',
    '6.5',
    '6.4',
    '6.3',
    '6.2',
    '6.1',
    '6.0',
    '5.9',
    '5.8',
    '5.7',
    '5.6',
    '5.5',
    '5.4',
    '5.3',
    '5.2',
    '5.1',
    '5.0',
];

function BlueprintGenerator() {
    const [blueprint, setBlueprint] = useState<Blueprint>({
        name: '',
        landingPage: '/wp-admin/',
        preferredVersions: {
            php: '8.2',
            wp: '6.8',
        },
        features: {
            networking: true,
        },
        steps: [
            {
                step: 'install-plugin',
                plugin: 'default-plugin',
            },
        ],
    });

    const [searchQuery, setSearchQuery] = useState<string>('');
    const [searchResults, setSearchResults] = useState<Plugin[]>([]);
    const [isSearching, setIsSearching] = useState<boolean>(false);
    const [isSaved, setIsSaved] = useState<boolean>(false);
    const [savedId, setSavedId] = useState<string | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [validationErrors, setValidationErrors] = useState<{
        name?: string;
        'preferredVersions.php'?: string;
        'preferredVersions.wp'?: string;
        landingPage?: string;
        'features.networking'?: string;
        steps?: string;
        status?: string;
    }>({});

    const searchPlugins = async (query: string) => {
        if (!query.trim()) {
            setSearchResults([]);
            return;
        }

        setIsSearching(true);
        try {
            const response = await fetch(
                `https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[search]=${encodeURIComponent(query)}&request[per_page]=10`,
            );
            const data = await response.json();
            setSearchResults(data.plugins || []);
        } catch (error) {
            console.error('Error searching plugins:', error);
            setSearchResults([]);
        } finally {
            setIsSearching(false);
        }
    };

    const addStep = () => {
        setBlueprint((prev) => ({
            ...prev,
            steps: [
                ...prev.steps,
                { step: 'login', username: '', password: '', plugin: '' },
            ],
        }));
    };

    const removeStep = (index: number) => {
        setBlueprint((prev) => ({
            ...prev,
            steps: prev.steps.filter((_, i) => i !== index),
        }));
    };

    const updateStep = (index: number, field: string, value: string) => {
        setBlueprint((prev) => ({
            ...prev,
            steps: prev.steps.map((step, i) =>
                i === index ? { ...step, [field]: value } : step,
            ),
        }));
    };

    const validateName = (name: string): string | undefined => {
        if (!name.trim()) {
            return 'Blueprint name is required';
        }
        if (name.trim().length < 3) {
            return 'Blueprint name must be at least 3 characters long';
        }
        if (name.trim().length > 100) {
            return 'Blueprint name must not exceed 100 characters';
        }
        return undefined;
    };

    const handleNameChange = (value: string) => {
        setBlueprint((prev) => ({
            ...prev,
            name: value,
        }));

        // Clear validation error when user starts typing
        if (validationErrors.name) {
            setValidationErrors((prev) => ({
                ...prev,
                name: undefined,
            }));
        }

        // Validate on blur or when field is not empty
        if (value.trim()) {
            const error = validateName(value);
            if (error) {
                setValidationErrors((prev) => ({
                    ...prev,
                    name: error,
                }));
            }
        }
    };

    const handleNameBlur = () => {
        const error = validateName(blueprint.name);
        setValidationErrors((prev) => ({
            ...prev,
            name: error,
        }));
    };

    const saveBlueprint = () => {
        // Validate name before saving
        const nameError = validateName(blueprint.name);
        if (nameError) {
            setValidationErrors({ name: nameError });
            return;
        }

        router.post(
            route('blueprints.store'),
            {
                ...blueprint,
                status: 'public', // Add default status
            },
            {
                onSuccess: (page) => {
                    // Clear validation errors on success
                    setValidationErrors({});
                    const responseData = page.props.data as unknown;
                    setSavedId(responseData?.id || null);
                    setIsSaved(true);
                    setError(null);
                },
                onError: (errors) => {
                    // Handle validation errors
                    setValidationErrors(errors);
                    setError('Validation failed');
                },
            },
        );
    };

    const copyToClipboard = () => {
        navigator.clipboard.writeText(JSON.stringify(blueprint, null, 2));
    };

    const openInPlayground = () => {
        const playgroundUrl = `https://playground.wordpress.net/?blueprint=${savedId}`;
        window.open(playgroundUrl, '_blank');
    };

    const downloadBlueprint = () => {
        // Validate name before downloading
        const nameError = validateName(blueprint.name);
        if (nameError) {
            setValidationErrors({ name: nameError });
            return;
        }

        const blob = new Blob([JSON.stringify(blueprint, null, 2)], {
            type: 'application/json',
        });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'blueprint.json';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    const renderStepFields = (
        step: {
            step: string;
            username?: string;
            password?: string;
            plugin?: string;
        },
        index: number,
    ) => {
        const username = step.username || '';
        const password = step.password || '';

        if (step.step === 'install-plugin') {
            return (
                <div className="col-span-3">
                    <div className="space-y-4">
                        <div className="relative">
                            <label
                                htmlFor={`search-plugin-${index}`}
                                className="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300"
                            >
                                Search Plugin
                            </label>
                            <div className="relative">
                                <input
                                    id={`search-plugin-${index}`}
                                    type="text"
                                    value={searchQuery}
                                    onChange={(e) => {
                                        setSearchQuery(e.target.value);
                                        searchPlugins(e.target.value);
                                    }}
                                    placeholder="Search WordPress plugins..."
                                    className="w-full rounded-md border-gray-300 py-2 pl-10 pr-4 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-400"
                                />
                                <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    {isSearching ? (
                                        <Loader2 className="h-4 w-4 animate-spin text-gray-400" />
                                    ) : (
                                        <Search className="h-4 w-4 text-gray-400" />
                                    )}
                                </div>
                            </div>
                        </div>

                        {searchResults.length > 0 && (
                            <div className="mt-2 max-h-60 divide-y overflow-y-auto rounded-md border">
                                {searchResults.map((plugin) => (
                                    <div
                                        key={plugin.slug}
                                        className="cursor-pointer p-3 hover:bg-gray-50"
                                        onClick={() => {
                                            updateStep(
                                                index,
                                                'plugin',
                                                plugin.slug,
                                            );
                                            setSearchQuery('');
                                            setSearchResults([]);
                                        }}
                                        role="button"
                                        tabIndex={0}
                                        onKeyDown={(e) => {
                                            if (
                                                e.key === 'Enter' ||
                                                e.key === ' '
                                            ) {
                                                updateStep(
                                                    index,
                                                    'plugin',
                                                    plugin.slug,
                                                );
                                                setSearchQuery('');
                                                setSearchResults([]);
                                            }
                                        }}
                                    >
                                        <div className="text-sm font-medium">
                                            {plugin.name}
                                        </div>
                                        <div className="mt-1 text-xs text-gray-500">
                                            {plugin.short_description}
                                        </div>
                                        <div className="mt-1 text-xs text-gray-400">
                                            Version: {plugin.version}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        {step.plugin && (
                            <div className="mt-4 rounded-md bg-gray-100 p-3 dark:bg-gray-600">
                                <div className="text-sm font-medium dark:text-gray-200">
                                    Selected Plugin:
                                </div>
                                <div className="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                    {step.plugin}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            );
        }

        return (
            <>
                <div>
                    <label
                        htmlFor={`username-${index}`}
                        className="block text-xs font-medium text-gray-700 dark:text-gray-300"
                    >
                        Username
                    </label>
                    <input
                        id={`username-${index}`}
                        type="text"
                        value={username}
                        onChange={(e) =>
                            updateStep(index, 'username', e.target.value)
                        }
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-400"
                    />
                </div>
                <div>
                    <label
                        htmlFor={`password-${index}`}
                        className="block text-xs font-medium text-gray-700 dark:text-gray-300"
                    >
                        Password
                    </label>
                    <input
                        id={`password-${index}`}
                        type="password"
                        value={password}
                        onChange={(e) =>
                            updateStep(index, 'password', e.target.value)
                        }
                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-400"
                    />
                </div>
            </>
        );
    };

    return (
        <div className="min-h-screen bg-gray-50 px-4 py-12 sm:px-6 lg:px-8 dark:bg-gray-900">
            <ErrorHandler error={error} onClose={() => setError(null)} />
            <div className="mx-auto max-w-3xl">
                <div className="space-y-8 rounded-lg bg-white p-6 shadow-lg dark:bg-gray-800">
                    <div className="flex items-center justify-between">
                        <div className="flex space-x-2">
                            <button
                                type="button"
                                onClick={saveBlueprint}
                                className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                <Save className="mr-2 h-4 w-4" />
                                Save
                            </button>
                            <button
                                type="button"
                                onClick={downloadBlueprint}
                                className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                <Download className="mr-2 h-4 w-4" />
                                Download
                            </button>
                            {isSaved && (
                                <>
                                    <button
                                        type="button"
                                        onClick={copyToClipboard}
                                        className="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                                    >
                                        <Copy className="mr-2 h-4 w-4" />
                                        Copy
                                    </button>
                                    <button
                                        type="button"
                                        onClick={openInPlayground}
                                        className="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                                    >
                                        <ExternalLink className="mr-2 h-4 w-4" />
                                        Open in Playground
                                    </button>
                                </>
                            )}
                        </div>
                    </div>

                    <div className="space-y-6">
                        <div>
                            <label
                                htmlFor="blueprint-name"
                                className="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Blueprint Name
                            </label>
                            <input
                                id="blueprint-name"
                                type="text"
                                value={blueprint.name}
                                onChange={(e) =>
                                    handleNameChange(e.target.value)
                                }
                                onBlur={handleNameBlur}
                                className={`mt-1 block w-full rounded-md shadow-sm focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 ${
                                    validationErrors.name
                                        ? 'border-red-300 focus:border-red-500 dark:border-red-600 dark:focus:border-red-500'
                                        : 'border-gray-300 focus:border-indigo-500 dark:border-gray-600 dark:focus:border-indigo-400'
                                }`}
                                placeholder="Enter a name for your blueprint"
                            />
                            <InputError
                                message={validationErrors.name}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <label
                                htmlFor="landing-page"
                                className="block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Landing Page
                            </label>
                            <input
                                id="landing-page"
                                type="text"
                                value={blueprint.landingPage}
                                onChange={(e) =>
                                    setBlueprint((prev) => ({
                                        ...prev,
                                        landingPage: e.target.value,
                                    }))
                                }
                                className={`mt-1 block w-full rounded-md shadow-sm focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 ${
                                    validationErrors.landingPage
                                        ? 'border-red-300 focus:border-red-500 dark:border-red-600 dark:focus:border-red-500'
                                        : 'border-gray-300 focus:border-indigo-500 dark:border-gray-600 dark:focus:border-indigo-400'
                                }`}
                            />
                            <InputError
                                message={validationErrors.landingPage}
                                className="mt-2"
                            />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    htmlFor="php-version"
                                    className="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    PHP Version
                                </label>
                                <select
                                    id="php-version"
                                    value={blueprint.preferredVersions.php}
                                    onChange={(e) =>
                                        setBlueprint((prev) => ({
                                            ...prev,
                                            preferredVersions: {
                                                ...prev.preferredVersions,
                                                php: e.target.value,
                                            },
                                        }))
                                    }
                                    className={`mt-1 block w-full rounded-md shadow-sm focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white ${
                                        validationErrors[
                                            'preferredVersions.php'
                                        ]
                                            ? 'border-red-300 focus:border-red-500 dark:border-red-600 dark:focus:border-red-500'
                                            : 'border-gray-300 focus:border-indigo-500 dark:border-gray-600 dark:focus:border-indigo-400'
                                    }`}
                                >
                                    {phpVersions.map((version) => (
                                        <option key={version} value={version}>
                                            {version}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={
                                        validationErrors[
                                            'preferredVersions.php'
                                        ]
                                    }
                                    className="mt-2"
                                />
                            </div>

                            <div>
                                <label
                                    htmlFor="wp-version"
                                    className="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    WordPress Version
                                </label>
                                <select
                                    id="wp-version"
                                    value={blueprint.preferredVersions.wp}
                                    onChange={(e) =>
                                        setBlueprint((prev) => ({
                                            ...prev,
                                            preferredVersions: {
                                                ...prev.preferredVersions,
                                                wp: e.target.value,
                                            },
                                        }))
                                    }
                                    className={`mt-1 block w-full rounded-md shadow-sm focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white ${
                                        validationErrors['preferredVersions.wp']
                                            ? 'border-red-300 focus:border-red-500 dark:border-red-600 dark:focus:border-red-500'
                                            : 'border-gray-300 focus:border-indigo-500 dark:border-gray-600 dark:focus:border-indigo-400'
                                    }`}
                                >
                                    {wordpressVersions.map((version) => (
                                        <option key={version} value={version}>
                                            {version}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={
                                        validationErrors['preferredVersions.wp']
                                    }
                                    className="mt-2"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="inline-flex items-center">
                                <input
                                    type="checkbox"
                                    checked={blueprint.features.networking}
                                    onChange={(e) =>
                                        setBlueprint((prev) => ({
                                            ...prev,
                                            features: {
                                                ...prev.features,
                                                networking: e.target.checked,
                                            },
                                        }))
                                    }
                                    className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-indigo-400"
                                />
                                <span className="ml-2 text-sm text-gray-600 dark:text-gray-300">
                                    Enable Networking
                                </span>
                            </label>
                        </div>

                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Steps
                                </h2>
                                <button
                                    type="button"
                                    onClick={addStep}
                                    className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-3 py-2 text-sm font-medium leading-4 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    <Plus className="mr-1 h-4 w-4" />
                                    Add Step
                                </button>
                            </div>

                            {blueprint.steps.map((step, index) => (
                                <div
                                    key={index}
                                    className="space-y-3 rounded-md bg-gray-50 p-4 dark:bg-gray-700"
                                >
                                    <div className="flex items-center justify-between">
                                        <h3 className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Step {index + 1}
                                        </h3>
                                        <button
                                            onClick={() => removeStep(index)}
                                            className="text-red-600 hover:text-red-700"
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </button>
                                    </div>
                                    <div className="grid grid-cols-3 gap-3">
                                        <div>
                                            <label
                                                htmlFor={`step-type-${index}`}
                                                className="block text-xs font-medium text-gray-700 dark:text-gray-300"
                                            >
                                                Step Type
                                            </label>
                                            <select
                                                id={`step-type-${index}`}
                                                value={step.step}
                                                onChange={(e) =>
                                                    updateStep(
                                                        index,
                                                        'step',
                                                        e.target.value,
                                                    )
                                                }
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-indigo-400"
                                            >
                                                <option value="login">
                                                    Login
                                                </option>
                                                <option value="install-plugin">
                                                    Install Plugin
                                                </option>
                                            </select>
                                        </div>
                                        {renderStepFields(step, index)}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default BlueprintGenerator;
