@extends('layouts.app')

@section('title', 'Upload XML Files')

@section('content')
    <h2 style="margin-bottom: 20px;">Upload Lottery XML Files</h2>

    <div class="upload-section">
        <form action="{{ route('uploads.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf

            <div class="dropzone" id="dropzone">
                <input type="file" name="files[]" id="fileInput" accept=".xml,text/xml" multiple style="display: none;">
                <div class="dropzone-content">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <h3>Drag & Drop XML Files Here</h3>
                    <p>or <span class="browse-btn">click to browse</span></p>
                    <p class="file-requirement">Upload 1-16 XML files. Complete batches require all 8 lottery types for a
                        date.</p>
                </div>
            </div>

            <div id="fileList" class="file-list" style="display: none;">
                <h4>Selected Files (<span id="fileCount">0</span>)</h4>
                <div id="dateBuckets"></div>
                <div id="fileItems"></div>
            </div>

            <div id="validationMessages" style="margin-top: 15px;"></div>

            <button type="submit" class="btn btn-primary btn-large" id="uploadBtn" disabled>
                Upload Files
            </button>
        </form>
    </div>

    <hr style="margin: 40px 0; border: none; border-top: 1px solid var(--border-color);">

    <h3 style="margin-bottom: 20px;">Recent Uploads</h3>

    @if($batches->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Batch ID</th>
                    <th>Uploaded By</th>
                    <th>Draw Date</th>
                    <th>Uploaded Date</th>
                    <th>Files</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batches as $batch)
                    <tr>
                        <td>#{{ $batch->id }}</td>
                        <td>{{ $batch->user->name }}</td>
                        <td>
                            @if($batch->draw_date)
                                <strong>{{ \Carbon\Carbon::parse($batch->draw_date)->format('Y-m-d') }}</strong>
                            @else
                                <span style="color: var(--text-light);">N/A</span>
                            @endif
                        </td>
                        <td>{{ $batch->created_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $batch->total_files }}</td>
                        <td>
                            <span class="badge badge-{{ $batch->status }}">
                                {{ ucfirst($batch->status) }}
                            </span>
                        </td>
                        <td>
                            {{ $batch->processed_files }}/{{ $batch->total_files }}
                            @if($batch->failed_files > 0)
                                <span style="color: var(--primary-color);">({{ $batch->failed_files }} failed)</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('uploads.show', $batch) }}" class="btn btn-primary"
                                style="padding: 6px 12px; font-size: 12px;">View</a>
                            @if(in_array(Auth::user()->role, ['admin', 'super_admin']))
                                <form action="{{ route('uploads.destroy', $batch) }}" method="POST" style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this batch?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-secondary"
                                        style="padding: 6px 12px; font-size: 12px; background-color: var(--primary-color);">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $batches->links() }}
        </div>
    @else
        <p style="color: var(--text-light); text-align: center; padding: 40px;">No uploads yet. Upload your first batch above!
        </p>
    @endif
@endsection

@push('styles')
    <style>
        .upload-section {
            margin-bottom: 30px;
        }

        .dropzone {
            border: 3px dashed var(--border-color);
            border-radius: 10px;
            padding: 60px 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: var(--bg-body);
        }

        .dropzone:hover,
        .dropzone.dragover {
            border-color: var(--primary-color);
            background: #fff3f3;
        }

        .dropzone-content svg {
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .dropzone-content h3 {
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .dropzone-content p {
            color: var(--text-light);
            margin: 5px 0;
        }

        .browse-btn {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: underline;
        }

        .file-requirement {
            font-size: 13px;
            font-weight: 600;
            color: var(--accent-color) !important;
            margin-top: 15px !important;
        }

        .file-list {
            margin-top: 20px;
            padding: 20px;
            background: var(--bg-body);
            border-radius: 10px;
        }

        .file-list h4 {
            margin-bottom: 15px;
            color: var(--text-color);
        }

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: white;
            border-radius: 5px;
            margin-bottom: 8px;
        }

        .file-name {
            flex: 1;
            color: var(--text-color);
        }

        .lottery-code {
            background: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 10px;
        }

        .remove-file {
            color: var(--primary-color);
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

        .btn-large {
            width: 100%;
            margin-top: 20px;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
        }

        .btn:disabled {
            background: #d1d5db !important;
            cursor: not-allowed;
        }

        .bucket-preview {
            background: #fff8e1;
            border: 1px solid var(--secondary-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .bucket-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const dateBuckets = document.getElementById('dateBuckets');
        const fileItems = document.getElementById('fileItems');
        const fileCount = document.getElementById('fileCount');
        const uploadBtn = document.getElementById('uploadBtn');
        const validationMessages = document.getElementById('validationMessages');

        const REQUIRED_LOTTERIES = ['AK', 'DS', 'LW', 'SB', 'KP', 'JS', 'SR', 'SF'];
        const LOTTERY_NAMES = {
            'AK': 'Ada Kotipathi',
            'DS': 'Supiri Dhana Sampatha',
            'LW': 'Lagna Wasanawa',
            'SB': 'Super Ball',
            'KP': 'Kapruka',
            'JS': 'Jaya Sampatha',
            'SR': 'Sasiri',
            'SF': 'Shanida'
        };

        let selectedFiles = [];

        // Click to browse
        dropzone.addEventListener('click', () => fileInput.click());

        // Drag and drop events
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('dragover');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            selectedFiles = Array.from(files);
            updateFileList();
            validateFiles();
        }

        function updateFileList() {
            if (selectedFiles.length === 0) {
                fileList.style.display = 'none';
                return;
            }

            fileList.style.display = 'block';
            fileCount.textContent = selectedFiles.length;

            // Group files by date (simulated - in reality we'd need to parse XML)
            // For now, just show all files in one list with lottery code extraction
            displayDateBuckets();
        }

        function displayDateBuckets() {
            const buckets = groupFilesByLotteryCode();
            dateBuckets.innerHTML = '';

            if (Object.keys(buckets).length > 0) {
                dateBuckets.innerHTML = `
                        <div class="bucket-preview">
                            <strong>File Summary:</strong>
                            <div style="margin-top: 10px;">
                                ${Object.entries(buckets).map(([code, files]) => `
                                    <span class="lottery-code" style="margin: 2px;">${code} (${files.length})</span>
                                `).join('')}
                            </div>
                        </div>
                    `;
            }

            // Display all files
            fileItems.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const lotteryCode = extractLotteryCode(file.name);
                const item = document.createElement('div');
                item.className = 'file-item';
                item.innerHTML = `
                        <span class="file-name">${file.name}</span>
                        ${lotteryCode ? `<span class="lottery-code">${lotteryCode}</span>` : ''}
                        <span class="remove-file" data-index="${index}">×</span>
                    `;
                fileItems.appendChild(item);
            });

            // Add remove event listeners
            document.querySelectorAll('.remove-file').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const index = parseInt(e.target.dataset.index);
                    selectedFiles.splice(index, 1);
                    updateFileList();
                    validateFiles();
                });
            });
        }

        function groupFilesByLotteryCode() {
            const groups = {};
            selectedFiles.forEach(file => {
                const code = extractLotteryCode(file.name);
                if (code) {
                    if (!groups[code]) groups[code] = [];
                    groups[code].push(file);
                }
            });
            return groups;
        }

        function extractLotteryCode(filename) {
            const match = filename.match(/^([A-Z]{2})_XML_/);
            return match ? match[1] : null;
        }

        function validateFiles() {
            validationMessages.innerHTML = '';
            const errors = [];
            const warnings = [];

            // Check max 16 files
            if (selectedFiles.length > 16) {
                errors.push(`You have selected ${selectedFiles.length} files. Maximum 16 files allowed.`);
                uploadBtn.disabled = true;
                displayMessages(errors, warnings);
                return;
            }

            if (selectedFiles.length === 0) {
                uploadBtn.disabled = true;
                return;
            }

            // Check for valid filenames
            const codes = selectedFiles.map(f => extractLotteryCode(f.name));
            const invalidFiles = selectedFiles.filter((f, i) => !codes[i]);

            if (invalidFiles.length > 0) {
                errors.push(`Invalid filename format for ${invalidFiles.length} file(s). Expected: XX_XML_*.xml`);
            }

            // Check for duplicates
            const validCodes = codes.filter(Boolean);
            const duplicates = validCodes.filter((code, index) => validCodes.indexOf(code) !== index);

            if (duplicates.length > 0) {
                errors.push(`Duplicate lottery types: ${[...new Set(duplicates)].join(', ')}. Remove duplicates before uploading.`);
            }

            // Check for unknown codes
            const unknownCodes = validCodes.filter(code => !REQUIRED_LOTTERIES.includes(code));
            if (unknownCodes.length > 0) {
                errors.push(`Unknown lottery codes: ${[...new Set(unknownCodes)].join(', ')}`);
            }

            // Check completeness - require exactly 8 or 16 files with complete batches
            const uniqueCodes = [...new Set(validCodes.filter(code => REQUIRED_LOTTERIES.includes(code)))];
            const missing = REQUIRED_LOTTERIES.filter(code => !uniqueCodes.includes(code));

            if (errors.length === 0) {
                if (uniqueCodes.length === 8 && selectedFiles.length === 8) {
                    warnings.push(`Complete batch: All 8 lotteries present (1 date)`);
                    uploadBtn.disabled = false;
                } else if (uniqueCodes.length === 8 && selectedFiles.length === 16) {
                    warnings.push(`Complete batches: All 8 lotteries × 2 dates`);
                    uploadBtn.disabled = false;
                } else if (selectedFiles.length === 8 || selectedFiles.length === 16) {
                    errors.push(`You must upload a complete batch. Missing: ${missing.map(c => LOTTERY_NAMES[c] || c).join(', ')}`);
                    uploadBtn.disabled = true;
                } else {
                    errors.push(`Invalid file count. Upload exactly 8 files (1 complete batch) or 16 files (2 complete batches).`);
                    uploadBtn.disabled = true;
                }
            } else {
                uploadBtn.disabled = true;
            }

            displayMessages(errors, warnings);
        }

        function displayMessages(errors, warnings) {
            let html = '';

            if (errors.length > 0) {
                html += `
                        <div class="alert alert-error">
                            ${errors.map(e => `<p>${e}</p>`).join('')}
                        </div>
                    `;
            }

            if (warnings.length > 0) {
                html += `
                        <div class="alert ${errors.length > 0 ? 'alert-warning' : 'alert-success'}">
                            ${warnings.map(w => `<p>${w}</p>`).join('')}
                        </div>
                    `;
            }

            validationMessages.innerHTML = html;
        }

        // Handle form submission to include drag-and-dropped files
        const uploadForm = document.getElementById('uploadForm');
        uploadForm.addEventListener('submit', (e) => {
            if (selectedFiles.length > 0) {
                e.preventDefault();

                const formData = new FormData();

                // Add CSRF token
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                // Add all selected files
                selectedFiles.forEach(file => {
                    formData.append('files[]', file);
                });

                // Submit via fetch
                uploadBtn.disabled = true;
                uploadBtn.textContent = 'Uploading...';

                fetch(uploadForm.action, {
                    method: 'POST',
                    body: formData,
                    redirect: 'follow'
                })
                .then(response => {
                    if (response.ok || response.redirected) {
                        // Success - redirect to the response URL or reload
                        window.location.href = response.url;
                    } else {
                        return response.text().then(text => {
                            throw new Error('Upload failed: ' + text);
                        });
                    }
                })
                .catch(error => {
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = 'Upload Files';
                    alert('Upload failed. Please try again.');
                    console.error(error);
                });
            }
        });
    </script>
@endpush
