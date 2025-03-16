@extends('backend.layouts.app')

@section('title', 'Compose Message')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Compose New Message</h3>
                <div class="card-action">
                    <a href="{{ route('messages.inbox') }}" class="btn btn-info">Inbox</a>
                    <a href="{{ route('messages.sent') }}" class="btn btn-info">Sent</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="recipient_type">Recipient Type <span class="text-danger">*</span></label>
                        <select class="form-control @error('recipient_type') is-invalid @enderror" id="recipient_type"
                            name="recipient_type" required>
                            <option value="">Select Recipient Type</option>
                            @if(auth()->user()->role == 2)
                            <option value="class_students" {{ old('recipient_type')=='class_students' ? 'selected' : ''
                                }}>All Students in My Class</option>
                            <option value="single_student" {{ old('recipient_type')=='single_student' ? 'selected' : ''
                                }}>Single Student</option>
                            <option value="class_parents" {{ old('recipient_type')=='class_parents' ? 'selected' : ''
                                }}>All Parents of My Class</option>
                            @else
                            <option value="admin" {{ old('recipient_type')=='admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ old('recipient_type')=='teacher' ? 'selected' : '' }}>Teacher
                            </option>
                            <option value="parent" {{ old('recipient_type')=='parent' ? 'selected' : '' }}>Parent
                            </option>
                            <option value="student" {{ old('recipient_type')=='student' ? 'selected' : '' }}>Student
                            </option>
                            <option value="all" {{ old('recipient_type')=='all' ? 'selected' : '' }}>All Users</option>
                            @endif
                        </select>
                        @error('recipient_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(auth()->user()->role == 2)
                    <div class="form-group" id="class_select_group" style="display: none;">
                        <label for="class_id">Select Class <span class="text-danger">*</span></label>
                        <select class="form-control @error('class_id') is-invalid @enderror" id="class_id"
                            name="class_id">
                            <option value="">Select Class</option>
                            @foreach($teacher_classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id')==$class->id ? 'selected' : '' }}>{{
                                $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="form-group" id="recipient_id_group" style="display: none;">
                        <label for="recipient_id">Recipient <span class="text-danger">*</span></label>
                        <select class="form-control @error('recipient_id') is-invalid @enderror" id="recipient_id"
                            name="recipient_id">
                            <option value="">Select Recipient</option>
                            <!-- Options will be populated via JavaScript -->
                        </select>
                        @error('recipient_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="message_type">Message Type <span class="text-danger">*</span></label>
                        <select class="form-control @error('message_type') is-invalid @enderror" id="message_type"
                            name="message_type" required>
                            <option value="">Select Message Type</option>
                            <option value="general" {{ old('message_type')=='general' ? 'selected' : '' }}>General
                            </option>
                            <option value="alert" {{ old('message_type')=='alert' ? 'selected' : '' }}>Alert</option>
                            <option value="warning" {{ old('message_type')=='warning' ? 'selected' : '' }}>Warning
                            </option>
                            <option value="complaint" {{ old('message_type')=='complaint' ? 'selected' : '' }}>Complaint
                            </option>
                        </select>
                        @error('message_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject"
                            name="subject" value="{{ old('subject') }}" required>
                        @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="message">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('message') is-invalid @enderror" id="message"
                            name="message" rows="6" required>{{ old('message') }}</textarea>
                        @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Send Message</button>
                        <a href="{{ route('messages.inbox') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recipientTypeSelect = document.getElementById('recipient_type');
        const recipientIdGroup = document.getElementById('recipient_id_group');
        const recipientIdSelect = document.getElementById('recipient_id');
        @if(auth()->user()->role == 2)
        const classSelectGroup = document.getElementById('class_select_group');
        const classIdSelect = document.getElementById('class_id');
        @endif

        // Store all recipients by type
        const recipients = {
            @if(auth()->user()->role != 2)
            admin: [
                @foreach($admins as $admin)
                { id: {{ $admin->id }}, name: "{{ $admin->name }}" },
                @endforeach
            ],
            teacher: [
                @foreach($teachers as $teacher)
                { id: {{ $teacher->id }}, name: "{{ $teacher->name }}" },
                @endforeach
            ],
            parent: [
                @foreach($parents as $parent)
                { id: {{ $parent->id }}, name: "{{ $parent->name }}" },
                @endforeach
            ],
            student: [
                @foreach($students as $student)
                { id: {{ $student->id }}, name: "{{ $student->name }}" },
                @endforeach
            ]
            @endif
        };

        recipientTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            
            @if(auth()->user()->role == 2)
                // Reset required attributes and clear values
                classIdSelect.removeAttribute('required');
                recipientIdSelect.removeAttribute('required');
                recipientIdSelect.value = '';
                
                // Hide both groups initially
                classSelectGroup.style.display = 'none';
                recipientIdGroup.style.display = 'none';
                
                if (selectedType === 'class_students' || selectedType === 'class_parents') {
                    classSelectGroup.style.display = 'block';
                    classIdSelect.setAttribute('required', 'required');
                    // Clear and hide recipient selection for broadcast messages
                    recipientIdSelect.value = '';
                    recipientIdGroup.style.display = 'none';
                } else if (selectedType === 'single_student') {
                    classSelectGroup.style.display = 'block';
                    recipientIdGroup.style.display = 'block';
                    classIdSelect.setAttribute('required', 'required');
                    recipientIdSelect.setAttribute('required', 'required');
                }
            @else
                if (selectedType && selectedType !== 'all') {
                    recipientIdGroup.style.display = 'block';
                    recipientIdSelect.setAttribute('required', 'required');
                    populateRecipients(selectedType);
                } else {
                    recipientIdGroup.style.display = 'none';
                    recipientIdSelect.removeAttribute('required');
                    recipientIdSelect.value = '';
                }
            @endif
        });

        @if(auth()->user()->role == 2)
        classIdSelect.addEventListener('change', function() {
            const selectedClass = this.value;
            const selectedType = recipientTypeSelect.value;
            
            if (selectedType === 'single_student' && selectedClass) {
                fetch(`dash/api/class/${selectedClass}/students`)
                    .then(response => response.json())
                    .then(data => {
                        recipientIdSelect.innerHTML = '<option value="">Select Student</option>';
                        data.forEach(student => {
                            recipientIdSelect.innerHTML += `<option value="${student.id}">${student.name}</option>`;
                        });
                        recipientIdGroup.style.display = 'block';
                    });
            }
        });
        @else
        function populateRecipients(type) {
            recipientIdSelect.innerHTML = '<option value="">Select Recipient</option>';
            if (recipients[type]) {
                recipients[type].forEach(recipient => {
                    recipientIdSelect.innerHTML += `<option value="${recipient.id}">${recipient.name}</option>`;
                });
            }
        }
        @endif

        // Set initial state based on old input
        if (recipientTypeSelect.value) {
            recipientTypeSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection