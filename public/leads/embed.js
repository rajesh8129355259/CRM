(function() {
    // Create and inject CSS
    const style = document.createElement('style');
    style.textContent = `
        .lead-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .lead-form .form-group {
            margin-bottom: 1rem;
        }
        .lead-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        .lead-form input, .lead-form textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        .lead-form input:focus, .lead-form textarea:focus {
            outline: none;
            border-color: #3B82F6;
            ring: 2px;
            ring-color: #93C5FD;
        }
        .lead-form button {
            background-color: #3B82F6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        .lead-form button:hover {
            background-color: #2563EB;
        }
        .lead-form .error {
            color: #DC2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .lead-form .success {
            color: #059669;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    `;
    document.head.appendChild(style);

    // Create form HTML
    const formHtml = `
        <div class="lead-form">
            <form id="embedded-lead-form">
                <input type="hidden" name="_token" id="csrf_token">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company">
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>
                <button type="submit">Submit</button>
                <div id="form-message"></div>
            </form>
        </div>
    `;

    // Find the container and inject the form
    const container = document.getElementById('lead-form');
    if (container) {
        container.innerHTML = formHtml;

        // Handle form submission
        const form = document.getElementById('embedded-lead-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const messageDiv = document.getElementById('form-message');
            const submitButton = form.querySelector('button[type="submit"]');
            
            try {
                // Disable submit button
                submitButton.disabled = true;
                submitButton.textContent = 'Submitting...';

                // Get CSRF token
                const tokenResponse = await fetch('/csrf-token');
                if (!tokenResponse.ok) {
                    throw new Error('Failed to get CSRF token');
                }
                const { token } = await tokenResponse.json();
                console.log('Got CSRF token');

                // Prepare form data
                const formData = {
                    _token: token,
                    first_name: form.querySelector('#first_name').value,
                    last_name: form.querySelector('#last_name').value,
                    email: form.querySelector('#email').value,
                    phone: form.querySelector('#phone').value || null,
                    company: form.querySelector('#company').value || null,
                    notes: form.querySelector('#notes').value || null,
                    status: 'new'
                };

                console.log('Form data:', formData);

                // Send the request
                const response = await fetch('/leads/submit', {
                    method: 'POST',
                    body: JSON.stringify(formData),
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    }
                });

                console.log('Response status:', response.status);

                const data = await response.json();
                console.log('Response data:', data);

                if (response.ok) {
                    messageDiv.className = 'success';
                    messageDiv.textContent = 'Thank you! Your information has been submitted successfully.';
                    form.reset();
                } else {
                    messageDiv.className = 'error';
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat();
                        messageDiv.textContent = errorMessages.join(', ');
                    } else {
                        messageDiv.textContent = data.message || 'An error occurred. Please try again.';
                    }
                }
            } catch (error) {
                console.error('Form submission error:', error);
                messageDiv.className = 'error';
                messageDiv.textContent = 'Network error occurred. Please check your connection and try again.';
            } finally {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = 'Submit';
            }
        });
    }
})(); 