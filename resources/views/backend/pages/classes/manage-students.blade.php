@extends('backend.layouts.app')

@section('title', 'Manage Students in Class')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manage Students in Class: {{ $class->name }}</h3>
                <div class="card-action">
                    <a href="{{ route('classes.show', $class) }}" class="btn btn-secondary">Back to Class Details</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('classes.update-students', $class) }}" method="POST" id="manageStudentsForm">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h4 class="card-title">Class Information</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th width="30%">Class Name:</th>
                                            <td>{{ $class->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Grade/Year:</th>
                                            <td>{{ $class->grade_year }}</td>
                                        </tr>
                                        <tr>
                                            <th>Teacher:</th>
                                            <td>{{ $class->teacher ? $class->teacher->name : 'Not Assigned' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Capacity:</th>
                                            <td>{{ $class->capacity }}</td>
                                        </tr>
                                        <tr>
                                            <th>Current Students:</th>
                                            <td>
                                                <span class="badge badge-info" id="selectedCount">{{
                                                    $class->students->count() }}</span> /
                                                <span class="badge badge-secondary">{{ $class->capacity }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h4 class="card-title mb-0"><i class="zmdi zmdi-info-outline mr-2"></i> Instructions
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-3"><i class="zmdi zmdi-check-circle text-success mr-2"></i> Check
                                            the
                                            boxes next to students you want to assign to this class.</li>
                                        <hr class="my-2">
                                        <li class="mb-3 mt-3"><i class="zmdi zmdi-check-circle text-success mr-2"></i>
                                            Use the search box to filter students by name or ID.
                                        </li>
                                        <hr class="my-2">
                                        <li class="mb-3 mt-3"><i class="zmdi zmdi-check-circle text-success mr-2"></i>
                                            The maximum number of students allowed is <span
                                                class="badge badge-primary">{{
                                                $class->capacity }}</span>.</li>
                                        <hr class="my-2">
                                        <li class="mt-3"><i class="zmdi zmdi-alert-triangle text-warning mr-2"></i>
                                            Exceeding the capacity limit will prevent form submission.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label for="student_search" class="text-primary font-weight-bold mb-0">Select
                                Students</label>
                            <div class="input-group" style="max-width: 300px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-search"></i></span>
                                </div>
                                <input type="text" id="student_search" class="form-control"
                                    placeholder="Search students...">
                                <div class="input-group-append">
                                    <button type="button" id="search_button" class="btn btn-primary"
                                        onclick="searchStudents()">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </div>
                        <script>
                            function searchStudents() {
                                const searchTerm = document.getElementById('student_search').value.toLowerCase().trim();
                                const studentItems = document.querySelectorAll('.student-item');
                                let matchCount = 0;
                                
                                studentItems.forEach(item => {
                                    const studentName = item.dataset.name ? item.dataset.name.toLowerCase() : '';
                                    const studentId = item.dataset.id || '';
                                    
                                    if (studentName.includes(searchTerm) || studentId.includes(searchTerm)) {
                                        item.style.display = '';
                                        matchCount++;
                                        
                                        if (searchTerm.length > 0) {
                                            item.classList.add('highlight-search');
                                            setTimeout(() => item.classList.remove('highlight-search'), 1500);
                                        }
                                    } else {
                                        item.style.display = 'none';
                                    }
                                });

                                const existingAlert = document.querySelector('.search-result-alert');
                                if (existingAlert) existingAlert.remove();

                                if (searchTerm.length > 0) {
                                    const alertClass = matchCount > 0 ? 'alert-success' : 'alert-warning';
                                    const message = matchCount > 0 
                                        ? `Found ${matchCount} student(s) matching "${searchTerm}"`
                                        : `No students found matching "${searchTerm}"`;
                                        
                                    const alertHTML = `
                                        <div class="search-result-alert alert ${alertClass} alert-dismissible fade show mb-3">
                                            ${message}
                                            <button type="button" class="close" data-dismiss="alert" style="margin-top: 17px;" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>`;
                                    
                                    document.querySelector('.student-list-container').insertAdjacentHTML('beforebegin', alertHTML);
                                } else {
                                    studentItems.forEach(item => item.style.display = '');
                                }
                            }

                            document.addEventListener('DOMContentLoaded', () => {
                                const searchInput = document.getElementById('student_search');
                                const searchButton = document.getElementById('search_button');
                                const manageStudentsForm = document.getElementById('manageStudentsForm');
                                const capacity = {{ $class->capacity }};

                                // Search functionality
                                searchInput.addEventListener('keypress', (e) => {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();
                                        searchStudents();
                                    }
                                });

                                searchInput.addEventListener('input', function() {
                                    if (this.value === '') {
                                        const existingAlert = document.querySelector('.search-result-alert');
                                        if (existingAlert) existingAlert.remove();
                                        document.querySelectorAll('.student-item').forEach(item => item.style.display = '');
                                    }
                                });

                                // Update selected count
                                function updateSelectedCount() {
                                    const selectedCount = document.querySelectorAll('input.student-check:checked').length;
                                    const selectedCountElement = document.getElementById('selectedCount');
                                    selectedCountElement.textContent = selectedCount;
                                    
                                    selectedCountElement.classList.toggle('badge-danger', selectedCount > capacity);
                                    selectedCountElement.classList.toggle('badge-info', selectedCount <= capacity);
                                }

                                // Initialize count and add change listener
                                updateSelectedCount();
                                document.addEventListener('change', (e) => {
                                    if (e.target.classList.contains('student-check')) {
                                        updateSelectedCount();
                                    }
                                });

                                // Form submission handling
                                manageStudentsForm.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    
                                    const selectedCount = document.querySelectorAll('input.student-check:checked').length;
                                    
                                    if (selectedCount > capacity) {
                                        alert(`You cannot assign more than ${capacity} students to this class. You have selected ${selectedCount} students.`);
                                        return;
                                    }
                                    
                                    const submitBtn = document.getElementById('submitBtn');
                                    submitBtn.disabled = true;
                                    submitBtn.innerHTML = '<i class="zmdi zmdi-spinner zmdi-hc-spin mr-1"></i> Saving...';
                                    
                                    this.submit();
                                });
                            });
                        </script>
                        <div class="card">
                            <div class="card-body p-0">
                                <div class="student-list-container"
                                    style="max-height: 400px; overflow-y: auto; padding: 15px;">
                                    <div class="row">
                                        @foreach($students as $student)
                                        <div class="col-md-4 col-lg-3 student-item mb-3"
                                            data-name="{{ strtolower($student->name) }}" data-id="{{ $student->id }}">
                                            <div class="custom-control custom-checkbox student-checkbox">
                                                <input type="checkbox" class="custom-control-input student-check"
                                                    id="student_{{ $student->id }}" name="student_ids[]"
                                                    value="{{ $student->id }}" {{ in_array($student->id,
                                                $class->students->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="student_{{ $student->id }}">
                                                    <div class="student-card p-2 rounded">
                                                        <div class="d-flex align-items-center">
                                                            <div class="student-avatar mr-2 d-flex align-items-center justify-content-center"
                                                                style="width: 40px; height: 40px; border-radius: 50%; background-color: #4e73df; color: white;">
                                                                <i class="zmdi zmdi-account"></i>
                                                            </div>
                                                            <div class="student-info">
                                                                <div class="student-name font-weight-bold">{{
                                                                    $student->name }}</div>
                                                                <div class="student-details small">
                                                                    <span class="text-muted">ID: {{ $student->id
                                                                        }}</span>
                                                                    @if($student->roll_number)
                                                                    <span class="text-muted ml-1">| Roll: {{
                                                                        $student->roll_number }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('student_ids')
                        <div class="text-danger mt-2 font-italic">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="zmdi zmdi-save mr-1"></i> Save Changes
                        </button>
                        <a href="{{ route('classes.show', $class) }}" class="btn btn-secondary">
                            <i class="zmdi zmdi-close mr-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .student-card {
        border: 1px solid #e3e6f0;
        transition: all 0.3s ease;
    }

    .custom-control-input:checked~.custom-control-label .student-card {
        border-color: #4e73df;
        background-color: #eef2ff;
        box-shadow: 0 0 0 1px #4e73df;
    }

    .student-checkbox label {
        width: 100%;
        cursor: pointer;
    }

    .student-list-container {
        scrollbar-width: thin;
        scrollbar-color: #4e73df #f8f9fc;
    }

    .student-list-container::-webkit-scrollbar {
        width: 8px;
    }

    .student-list-container::-webkit-scrollbar-track {
        background: #f8f9fc;
    }

    .student-list-container::-webkit-scrollbar-thumb {
        background-color: #4e73df;
        border-radius: 20px;
        border: 2px solid #f8f9fc;
    }

    .student-avatar {
        flex-shrink: 0;
    }

    .student-info {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    kbd {
        background-color: #f7f7f7;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);
        color: #333;
        display: inline-block;
        font-size: 0.85em;
        font-weight: 700;
        line-height: 1;
        padding: 2px 4px;
        white-space: nowrap;
    }

    hr {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        margin-left: 0;
        margin-right: 0;
        width: 100%;
    }

    /* Highlight effect for search */
    .highlight-search {
        animation: highlight 1s ease-in-out;
    }

    @keyframes highlight {
        0% {
            background-color: #fff;
        }

        50% {
            background-color: #fffde7;
        }

        100% {
            background-color: #fff;
        }
    }
</style>
@endpush