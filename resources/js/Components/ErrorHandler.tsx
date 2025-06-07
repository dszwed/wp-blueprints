import { AlertCircle, CheckCircle } from 'lucide-react';
import React from 'react';

interface ErrorHandlerProps {
    error: string | null;
    onClose: () => void;
}

const ErrorHandler: React.FC<ErrorHandlerProps> = ({ error, onClose }) => {
    if (!error) return null;

    return (
        <div className="pointer-events-auto fixed bottom-4 right-4 w-full max-w-sm rounded-lg bg-white shadow-lg">
            <div className="flex items-start p-4">
                <div className="flex-shrink-0">
                    {error ? (
                        <AlertCircle
                            className="h-6 w-6 text-red-600"
                            aria-hidden="true"
                        />
                    ) : (
                        <CheckCircle
                            className="h-6 w-6 text-green-600"
                            aria-hidden="true"
                        />
                    )}
                </div>
                <div className="ml-3 w-0 flex-1 pt-0.5">
                    <p className="text-sm font-medium text-gray-900">
                        {error ? 'Error' : 'Success'}
                    </p>
                    <p className="mt-1 text-sm text-gray-500">{error}</p>
                </div>
                <div className="ml-4 flex flex-shrink-0">
                    <button
                        className="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        onClick={onClose}
                    >
                        <span className="sr-only">Close</span>
                        <svg
                            className="h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fillRule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clipRule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    );
};

export default ErrorHandler;
