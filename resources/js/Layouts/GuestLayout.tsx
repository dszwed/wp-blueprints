import NavigationWrapper from '@/Components/NavigationWrapper';
import { Link } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

interface GuestLayoutProps extends PropsWithChildren {
    narrow?: boolean;
}

export default function Guest({ children, narrow = false }: GuestLayoutProps) {
    const userContent = (
        <div className="flex items-center space-x-4">
            <Link
                href={route('login')}
                className="text-sm text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
            >
                Login
            </Link>
            <Link
                href={route('register')}
                className="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
            >
                Register
            </Link>
        </div>
    );

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <NavigationWrapper userContent={userContent} />

            <main className="flex flex-col items-center pt-6 sm:justify-center sm:pt-0">
                <div
                    className={`mt-6 ${narrow ? 'w-full max-w-md' : 'w-full'} overflow-hidden bg-white px-6 py-4 shadow-md sm:rounded-lg dark:bg-transparent`}
                >
                    {children}
                </div>
            </main>
        </div>
    );
}
