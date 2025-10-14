import { Inertia } from '@inertiajs/inertia';
import { useRef, useState } from 'react';
import '../../css/dashboard.css';
import '../../css/form.css';

const Upload = () => {
    const [files, setFiles] = useState([]);
    const fileInputRef = useRef(null);
    const [selectedDate, setSelectedDate] = useState("");

    // Handle file selection
    const handleFileChange = (e) => {
        setFiles([...e.target.files]); // Store selected files in the state
    };

    // Remove a single file from the selection
    const handleRemoveFile = (indexToRemove) => {
        const nextFiles = files.filter((_, index) => index !== indexToRemove);
        setFiles(nextFiles);
        if (nextFiles.length === 0 && fileInputRef.current) {
            // Reset native file input when no files remain
            fileInputRef.current.value = '';
        }
    };

    // Clear all selected files
    const handleClearAll = () => {
        setFiles([]);
        if (fileInputRef.current) fileInputRef.current.value = '';
    };

    // Handle file upload
    const handleUpload = (e) => {
        e.preventDefault();
        const formData = new FormData();

        // Append each file to the FormData object
        files.forEach((file, index) => {
            formData.append(`files[${index}]`, file);
        });

        Inertia.post('/upload', formData);
    };


    return (
        <div className="w-[500px] h-auto mx-auto">
            <div className="py-12">
                <div className="max-w-5xl mx-auto sm:px-4 lg:px-6">
                    <div className="form-container space-y-8">
                        {/* File Upload Form */}
                        <div>
                            <h3 className="form-heading">Upload Lottery XML Files</h3>
                            <form onSubmit={handleUpload} className="space-y-4">
                                <input
                                    type="file"
                                    accept=".xml"
                                    onChange={handleFileChange}
                                    multiple
                                    className="form-input"
                                    ref={fileInputRef}
                                />

                                {/* Display selected files */}
                                {files.length > 0 && (
                                    <div className="mt-4">
                                        <h4 className="text-lg font-semibold">Selected Files: <span className="text-blue-600">({files.length})</span></h4>
                                        <ul className="file-list">
                                            {files.map((file, index) => (
                                                <li key={index} className="file-item">
                                                    <span className="file-name">{file.name}</span>
                                                    <button
                                                        type="button"
                                                        aria-label="Remove file"
                                                        onClick={() => handleRemoveFile(index)}
                                                        className="file-remove-btn"
                                                    >
                                                        ×
                                                    </button>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                )}

                                <div className="actions">
                                    <button
                                        type="submit"
                                        className="submit-button bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                                    >
                                        Upload
                                    </button>
                                    {files.length > 0 && (
                                        <button
                                            type="button"
                                            aria-label="Clear all files"
                                            onClick={handleClearAll}
                                            className="clear-all-button"
                                        >
                                            <span className="icon">×</span>
                                            <span>Clear all selected files</span>
                                        </button>
                                    )}
                                </div>
                            </form>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
    );
};

export default Upload;
