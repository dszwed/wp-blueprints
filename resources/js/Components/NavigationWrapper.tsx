import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';

interface NavigationWrapperProps {
    navigationContent?: ReactNode;
    userContent?: ReactNode;
    mobileNavigationContent?: ReactNode;
    showMobileNavigation?: boolean;
}

export default function NavigationWrapper({
    navigationContent,
    userContent,
    mobileNavigationContent,
    showMobileNavigation = false,
}: NavigationWrapperProps) {
    return (
        <nav className="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="flex h-16 justify-between">
                    <div className="flex">
                        <div className="flex shrink-0 items-center">
                            <Link href="/">
                                <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            </Link>
                        </div>

                        {navigationContent && (
                            <div className="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                {navigationContent}
                            </div>
                        )}
                    </div>

                    {userContent && (
                        <div className="hidden sm:ms-6 sm:flex sm:items-center">
                            {userContent}
                        </div>
                    )}
                </div>
            </div>

            {mobileNavigationContent && (
                <div
                    className={
                        (showMobileNavigation ? 'block' : 'hidden') +
                        ' sm:hidden'
                    }
                >
                    {mobileNavigationContent}
                </div>
            )}
        </nav>
    );
}
