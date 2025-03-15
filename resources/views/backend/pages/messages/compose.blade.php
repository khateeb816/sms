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
                            <option value="admin" {{ old('recipient_type')=='admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ old('recipient_type')=='teacher' ? 'selected' : '' }}>Teacher
                            </option>
                            <option value="parent" {{ old('recipient_type')=='parent' ? 'selected' : '' }}>Parent
                            </option>
                            <option value="student" {{ old('recipient_type')=='student' ? 'selected' : '' }}>Student
                            </option>
                            <option value="all" {{ old('recipient_type')=='all' ? 'selected' : '' }}>All Users</option>
                        </select>
                        @error('recipient_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" id="bulk_options_group">
                        <label>Bulk Message Options</label>
                        <div class="bulk-options">
                            <button type="button" class="btn btn-outline-primary bulk-option" data-type="teacher">All
                                Teachers</button>
                            <button type="button" class="btn btn-outline-primary bulk-option" data-type="parent">All
                                Parents</button>
                            <button type="button" class="btn btn-outline-primary bulk-option" data-type="student">All
                                Students</button>
                            <button type="button" class="btn btn-outline-primary bulk-option"
                                data-type="all">Everyone</button>
                        </div>
                        <small class="form-text text-muted">Click to quickly select a bulk recipient group</small>
                    </div>

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

                    <div class="form-group" id="broadcast_group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_broadcast" name="is_broadcast"
                                value="1" {{ old('is_broadcast') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_broadcast">Send as broadcast to all selected
                                recipient type</label>
                        </div>
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

<style>
    .bulk-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
    }

    .bulk-option {
        transition: all 0.3s ease;
    }

    .bulk-option.active {
        background-color: #4e73df;
        color: white;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store all recipients by type
        const recipients = {
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
        };

        const recipientTypeSelect = document.getElementById('recipient_type');
        const recipientIdGroup = document.getElementById('recipient_id_group');
        const recipientIdSelect = document.getElementById('recipient_id');
        const isBroadcastCheckbox = document.getElementById('is_broadcast');
        const bulkOptions = document.querySelectorAll('.bulk-option');
        const broadcastGroup = document.getElementById('broadcast_group');

        // Function to update recipient options based on selected type
        function updateRecipientOptions() {
            const selectedType = recipientTypeSelect.value;
            
            // Clear existing options
            recipientIdSelect.innerHTML = '<option value="">Select Recipient</option>';
            
            // Check if any bulk option is active
            const isAnyBulkOptionActive = Array.from(bulkOptions).some(btn => btn.classList.contains('active'));
            
            // Show/hide recipient selection based on type and bulk option status
            if (selectedType && selectedType !== 'all' && !isAnyBulkOptionActive && !isBroadcastCheckbox.checked) {
                recipientIdGroup.style.display = 'block';
                
                // Populate options
                if (recipients[selectedType]) {
                    recipients[selectedType].forEach(recipient => {
                        const option = document.createElement('option');
                        option.value = recipient.id;
                        option.textContent = recipient.name;
                        recipientIdSelect.appendChild(option);
                    });
                }
                
                broadcastGroup.style.display = 'block';
            } else {
                if (selectedType === 'all') {
                    broadcastGroup.style.display = 'none';
                } else {
                    broadcastGroup.style.display = 'block';
                }
                
                if (isAnyBulkOptionActive || isBroadcastCheckbox.checked) {
                    recipientIdGroup.style.display = 'none';
                }
            }
            
            // Don't reset bulk option buttons here
        }

        // Update recipient options when type changes
        recipientTypeSelect.addEventListener('change', function() {
            // Reset bulk options when manually changing the dropdown
            if (!event.isTrusted) {
                // This is a programmatic change (from bulk option buttons), don't reset
            } else {
                // This is a user-initiated change, reset bulk options
                bulkOptions.forEach(btn => btn.classList.remove('active'));
            }
            
            updateRecipientOptions();
        });
        
        // Toggle recipient selection based on broadcast checkbox
        isBroadcastCheckbox.addEventListener('change', function() {
            if (this.checked) {
                recipientIdGroup.style.display = 'none';
            } else {
                const selectedType = recipientTypeSelect.value;
                if (selectedType && selectedType !== 'all') {
                    recipientIdGroup.style.display = 'block';
                }
            }
        });
        
        // Handle bulk option buttons
        bulkOptions.forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                
                // Update UI
                bulkOptions.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Set recipient type
                recipientTypeSelect.value = type;
                
                // For all bulk options, hide the recipient dropdown
                recipientIdGroup.style.display = 'none';
                
                // If it's a bulk message, check the broadcast checkbox
                if (type !== 'all') {
                    isBroadcastCheckbox.checked = true;
                    broadcastGroup.style.display = 'block';
                } else {
                    // For "Everyone" option, no need for broadcast checkbox
                    broadcastGroup.style.display = 'none';
                }
                
                // Trigger change event
                recipientTypeSelect.dispatchEvent(new Event('change'));
            });
        });
        
        // Initial update
        updateRecipientOptions();
        
        // Set initial state based on old input
        if (isBroadcastCheckbox.checked) {
            recipientIdGroup.style.display = 'none';
        }
    });
</script>
@endsection