interface Blueprint {
    id: string;
    name: string;
    description?: string;
    status: 'public' | 'private';
    php_version: string;
    wordpress_version: string;
    steps: unknown[];
    created_at: string;
    updated_at: string;
    is_anonymous: boolean;
    statistics?: {
        views_count: number;
        runs_count: number;
        last_viewed_at?: string;
        last_run_at?: string;
    };
}

interface BlueprintCardProps {
    blueprint: Blueprint;
    showActions?: boolean;
    showStatus?: boolean;
}

export default function BlueprintCard({
    blueprint,
    showActions = true,
    showStatus = true,
}: BlueprintCardProps) {
    const formatDate = (date: string) => {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    return (
        <div className="overflow-hidden bg-white shadow-sm transition-shadow duration-300 hover:shadow-lg sm:rounded-lg dark:bg-gray-800">
            <div className="p-6">
                <div className="mb-4 flex items-start justify-between">
                    <h3 className="truncate text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {blueprint.name}
                    </h3>
                    {showStatus && (
                        <span
                            className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${
                                blueprint.status === 'public'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'
                            }`}
                        >
                            {blueprint.status}
                        </span>
                    )}
                </div>

                {blueprint.description && (
                    <p className="mb-4 line-clamp-2 text-sm text-gray-600 dark:text-gray-400">
                        {blueprint.description}
                    </p>
                )}

                <div className="mb-4 space-y-2">
                    <div className="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <span className="font-medium">PHP:</span>
                        <span className="ml-1">{blueprint.php_version}</span>
                    </div>
                    <div className="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <span className="font-medium">WordPress:</span>
                        <span className="ml-1">{blueprint.wordpress_version}</span>
                    </div>
                    <div className="flex items-center text-sm text-gray-500 dark:text-gray-400">
                        <span className="font-medium">Steps:</span>
                        <span className="ml-1">{blueprint.steps?.length || 0}</span>
                    </div>
                </div>

                {blueprint.statistics && (
                    <div className="mb-4 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                        <div className="flex items-center">
                            <svg className="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path
                                    fillRule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clipRule="evenodd"
                                />
                            </svg>
                            {blueprint.statistics.views_count}
                        </div>
                        <div className="flex items-center">
                            <svg className="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    fillRule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                    clipRule="evenodd"
                                />
                            </svg>
                            {blueprint.statistics.runs_count}
                        </div>
                    </div>
                )}

                <div className="flex flex-col space-y-3">
                    <span className="text-xs text-gray-500 dark:text-gray-400">
                        Created {formatDate(blueprint.created_at)}
                    </span>
                    {showActions && (
                        <div className="flex flex-col space-y-2">
                            <a
                                href={`https://playground.wordpress.net/?blueprint-url=${encodeURIComponent(
                                    window.location.origin + '/blueprint/' + blueprint.id
                                )}`}
                                target="_blank"
                                rel="noreferrer"
                                className="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                            >
                                <svg
                                    className="mr-1 h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                    />
                                </svg>
                                Open in Playground
                            </a>
                            <button
                                onClick={() => {
                                    const link = document.createElement('a');
                                    link.href = `/blueprint/${blueprint.id}`;
                                    link.download = `${blueprint.name
                                        .replace(/[^a-z0-9]/gi, '_')
                                        .toLowerCase()}.json`;
                                    document.body.appendChild(link);
                                    link.click();
                                    document.body.removeChild(link);
                                }}
                                className="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                            >
                                <svg
                                    className="mr-1 h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                Download
                            </button>
                            <button
                                onClick={async () => {
                                    const url = `${window.location.origin}/blueprint/${blueprint.id}`;
                                    try {
                                        await navigator.clipboard.writeText(url);
                                        // You could add a toast notification here
                                    } catch (err) {
                                        // Fallback for older browsers
                                        const textArea = document.createElement('textarea');
                                        textArea.value = url;
                                        document.body.appendChild(textArea);
                                        textArea.select();
                                        document.execCommand('copy');
                                        document.body.removeChild(textArea);
                                    }
                                }}
                                className="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                            >
                                <svg
                                    className="mr-1 h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                                    />
                                </svg>
                                Copy URL
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
} 