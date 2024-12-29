(function() {
    // Create iframe element
    var iframe = document.createElement('iframe');
    iframe.src = '{{ route("public.leads.form") }}';
    iframe.style.width = '100%';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    iframe.style.overflow = 'hidden';

    // Add iframe to container
    var container = document.getElementById('lead-form-container');
    if (!container) {
        console.error('Lead form container not found. Please add a div with id="lead-form-container"');
        return;
    }
    container.appendChild(iframe);

    // Handle iframe resizing
    window.addEventListener('message', function(event) {
        if (event.origin !== '{{ config("app.url") }}') return;

        if (event.data.type === 'setHeight') {
            iframe.style.height = event.data.height + 'px';
        }
    });

    // Initial height calculation
    function calculateHeight() {
        if (iframe.contentWindow) {
            iframe.contentWindow.postMessage({ type: 'getHeight' }, '*');
        }
    }

    iframe.onload = calculateHeight;
    window.addEventListener('resize', calculateHeight);
})(); 