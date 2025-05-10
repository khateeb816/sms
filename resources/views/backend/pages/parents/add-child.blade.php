@extends('backend.layouts.app')

@section('title', 'Add Child to Parent')

@section('content')
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Add Child to {{ $parent->name }}</div>
                    <hr>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tab navigation -->
                    <ul class="nav nav-tabs" id="addChildTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="search-tab" data-toggle="tab" href="#search" role="tab"
                                aria-controls="search" aria-selected="true">Search Student</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="select-tab" data-toggle="tab" href="#select" role="tab"
                                aria-controls="select" aria-selected="false">Select from List</a>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content mt-3" id="addChildTabContent">
                        <!-- Search by Roll Number Tab -->
                        <div class="tab-pane fade show active" id="search" role="tabpanel" aria-labelledby="search-tab">
                            <div class="form-group row">
                                <label for="search_term" class="col-sm-2 col-form-label">Search</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="search_term"
                                        placeholder="Enter roll number or student name">
                                    <small class="form-text text-muted">Enter partial roll number or name to find matching
                                        students</small>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" onclick="searchStudents()"
                                        class="btn btn-primary">Search</button>
                                </div>
                            </div>

                            <!-- Multiple student results will appear here after search -->
                            <div id="search_results_container" class="mt-4" style="display: none;">
                                <h5 class="mb-3">Search Results</h5>
                                <div id="search_results_list" class="row">
                                    <!-- Results will be dynamically added here -->
                                </div>
                            </div>

                            <div id="no_results" class="alert alert-warning mt-4" style="display: none;">
                                No matching students found.
                            </div>
                        </div>

                        <!-- Select from List Tab -->
                        <div class="tab-pane fade" id="select" role="tabpanel" aria-labelledby="select-tab">
                            <form action="{{ url('/dash/parents/' . $parent->id . '/add-child') }}" method="POST">
                                @csrf
                                <div class="form-group row">
                                    <label for="student_id" class="col-sm-2 col-form-label">Select Student</label>
                                    <div class="col-sm-10">
                                        <select class="form-control @error('student_id') is-invalid @enderror"
                                            id="student_id" name="student_id" required onchange="showStudentDetails(this)">
                                            <option value="">-- Select Student --</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ old('student_id') == $student->id ? 'selected' : '' }}
                                                    data-name="{{ $student->name }}"
                                                    data-roll="{{ $student->roll_number ?? 'Not assigned' }}"
                                                    data-class="{{ $student->class ?? 'Not assigned' }}">
                                                    {{ $student->name }} - Roll:
                                                    {{ $student->roll_number ?? 'Not assigned' }}
                                                    (Class:
                                                    {{ $student->class ?? 'Not assigned' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('student_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror

                                        @if ($students->isEmpty())
                                            <div class="mt-2 text-warning">
                                                <i class="icon-info"></i> No students available. Please add students first.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Student details will appear here after selection -->
                                <div id="selection_details" class="mt-4" style="display: none;">
                                    <div class="card bg-dark">
                                        <div class="card-body">
                                            <h5 class="card-title">Selected Student</h5>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <img id="selected_student_image" src=""
                                                        class="rounded-circle img-thumbnail" alt="Student Image"
                                                        style="width: 80px; height: 80px;">
                                                </div>
                                                <div class="col-md-10">
                                                    <h5 id="selected_student_name"></h5>
                                                    <p class="mb-1"><strong>Class:</strong> <span
                                                            id="selected_student_class"></span></p>
                                                    <p class="mb-1"><strong>Roll Number:</strong> <span
                                                            id="selected_student_roll"></span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mt-4">
                                    <div class="col-sm-12 text-center">
                                        <button type="submit" class="btn btn-primary px-5"
                                            {{ $students->isEmpty() ? 'disabled' : '' }}>Add
                                            Child</button>
                                        <a href="{{ url('/dash/parents/' . $parent->id) }}"
                                            class="btn btn-light px-5">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inline JavaScript -->
    <script type="text/javascript">
        // Define student data
        var studentsData = [
            @foreach ($students as $student)
                {
                    id: {{ $student->id }},
                    name: "{{ $student->name }}",
                    roll: "{{ $student->roll_number ?? '' }}",
                    class: "{{ $student->class ?? 'Not assigned' }}"
                }
                @if (!$loop->last)
                    ,
                @endif
            @endforeach
        ];

        // Search function for multiple results
        function searchStudents() {
            var searchTerm = document.getElementById('search_term').value.trim().toLowerCase();

            if (!searchTerm) {
                alert('Please enter a roll number or name to search');
                return;
            }

            // Hide previous results
            document.getElementById('search_results_container').style.display = 'none';
            document.getElementById('no_results').style.display = 'none';

            // Clear previous results
            var resultsContainer = document.getElementById('search_results_list');
            resultsContainer.innerHTML = '';

            // Find students with matching roll number or name
            var matchingStudents = [];

            for (var i = 0; i < studentsData.length; i++) {
                var student = studentsData[i];
                // Check if roll number or name contains the search term
                if (
                    (student.roll && student.roll.toLowerCase().includes(searchTerm)) ||
                    student.name.toLowerCase().includes(searchTerm)
                ) {
                    matchingStudents.push(student);
                }
            }

            if (matchingStudents.length > 0) {
                // Display all matching students
                for (var j = 0; j < matchingStudents.length; j++) {
                    var student = matchingStudents[j];

                    // Create a card for each student
                    var studentCard = document.createElement('div');
                    studentCard.className = 'col-md-6 mb-3';

                    studentCard.innerHTML = `
                    <div class="card bg-dark">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(student.name)}&background=random" 
                                        class="rounded-circle img-thumbnail" alt="Student Image" style="width: 70px; height: 70px;">
                                </div>
                                <div class="col-md-9">
                                    <h5>${student.name}</h5>
                                    <p class="mb-1"><strong>Class:</strong> ${student.class}</p>
                                    <p class="mb-1"><strong>Roll Number:</strong> ${student.roll}</p>
                                    <form action="{{ url('/dash/parents/' . $parent->id . '/add-child') }}" method="POST" class="mt-2">
                                        @csrf
                                        <input type="hidden" name="student_id" value="${student.id}">
                                        <button type="submit" class="btn btn-success btn-sm">Add as Child</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    resultsContainer.appendChild(studentCard);
                }

                document.getElementById('search_results_container').style.display = 'block';
            } else {
                document.getElementById('no_results').style.display = 'block';
            }
        }

        // Handle Enter key in search field
        document.getElementById('search_term').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchStudents();
            }
        });

        // Show student details when selected from dropdown
        function showStudentDetails(selectElement) {
            if (selectElement.value) {
                var selectedOption = selectElement.options[selectElement.selectedIndex];

                // Get data from the selected option
                var name = selectedOption.getAttribute('data-name');
                var roll = selectedOption.getAttribute('data-roll');
                var className = selectedOption.getAttribute('data-class');

                // Display student details
                document.getElementById('selected_student_name').textContent = name;
                document.getElementById('selected_student_class').textContent = className;
                document.getElementById('selected_student_roll').textContent = roll;
                document.getElementById('selected_student_image').src = 'https://ui-avatars.com/api/?name=' +
                    encodeURIComponent(name) + '&background=random';
                document.getElementById('selection_details').style.display = 'block';
            } else {
                document.getElementById('selection_details').style.display = 'none';
            }
        }

        // Initialize dropdown if there's a selected value
        window.onload = function() {
            var studentSelect = document.getElementById('student_id');
            if (studentSelect.value) {
                showStudentDetails(studentSelect);
            }

            // Make sure tabs work properly
            $('#addChildTab a').on('click', function(e) {
                e.preventDefault();
                $(this).tab('show');
            });

            // Handle direct link to tabs
            if (window.location.hash) {
                $('#addChildTab a[href="' + window.location.hash + '"]').tab('show');
            }
        };
    </script>
@endsection
