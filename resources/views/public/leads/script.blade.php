(function() {
    // Create iframe element
    var iframe = document.createElement('iframe');
    iframe.src = '{{ route("public.leads.form") }}';
    iframe.style.width = '100%';
    iframe.style.height = '700px';
    iframe.style.border = 'none';
    iframe.style.overflow = 'hidden';

    // Find the script tag
    var script = document.currentScript;
    
    // Insert iframe after the script tag
    script.parentNode.insertBefore(iframe, script.nextSibling);

    // Handle iframe resizing
    window.addEventListener('message', function(e) {
        if (e.data.type === 'setHeight') {
            iframe.style.height = e.data.height + 'px';
        }
    });
})(); 