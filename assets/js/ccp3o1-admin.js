jQuery(document).ready(function($) {
    // Initialize media uploader
    $('#ccp3o1-add-media').click(function() {
        var mediaUploader = wp.media({
            title: 'Add Media to Carousel',
            button: { text: 'Add to Carousel' },
            multiple: true,
            library: { type: ['image', 'video'] }
        });

        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').toJSON();
            if (attachments.length === 0) {
                alert('Please select at least one image or video.');
                return;
            }

            var ids = attachments.map(function(attachment) {
                return attachment.id;
            }).join(',');
            $('#ccp3o1-media-ids').val(ids);

            var preview = '';
            attachments.forEach(function(attachment) {
                if (attachment.type === 'image') {
                    preview += '<img src="' + attachment.url + '" style="max-width:100px; margin:5px;">';
                } else if (attachment.type === 'video') {
                    preview += '<video src="' + attachment.url + '" style="max-width:100px; margin:5px;" controls></video>';
                }
            });
            $('#ccp3o1-media-preview').html(preview);
            console.log('Media IDs set:', ids);
        });

        mediaUploader.open();
    });

    // Validate form before submission
    $('#ccp3o1-form').on('submit', function(e) {
        var mediaIds = $('#ccp3o1-media-ids').val();
        if (!mediaIds) {
            alert('Please select at least one image or video.');
            e.preventDefault();
            return false;
        }
        var dimensions = $('input[name="ccp3o1_carousels[new_carousel][dimensions]"]').val();
        if (!dimensions.match(/^\d+x\d+$/)) {
            alert('Please enter valid dimensions (e.g., 500x800).');
            e.preventDefault();
            return false;
        }
        console.log('Form submitted with media IDs:', mediaIds);
    });

    // Initialize media uploader for edit form
    $('#ccp3o1-edit-add-media').click(function() {
        var mediaUploader = wp.media({
            title: 'Update Carousel Media',
            button: { text: 'Use in Carousel' },
            multiple: true,
            library: { type: ['image', 'video'] }
        });

        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').toJSON();
            var ids = attachments.map(function(attachment) { return attachment.id; }).join(',');
            $('#ccp3o1-edit-media-ids').val(ids);

            var preview = '';
            attachments.forEach(function(attachment) {
                if (attachment.type === 'image') {
                    preview += '<img src="' + attachment.url + '" style="max-width:100px; margin:5px;">';
                } else if (attachment.type === 'video') {
                    preview += '<video src="' + attachment.url + '" style="max-width:100px; margin:5px;" controls></video>';
                }
            });
            $('#ccp3o1-edit-media-preview').html(preview);
            console.log('Edit Media IDs set:', ids);
        });

        mediaUploader.open();
    });

    // Edit form validation
    $('#ccp3o1-edit-form').on('submit', function(e) {
        var dimensions = $('input[name="ccp3o1_edit_carousel[dimensions]"]').val();
        if (!dimensions.match(/^\d+x\d+$/)) {
            alert('Please enter valid dimensions (e.g., 500x800).');
            e.preventDefault();
            return false;
        }
    });

    // Debug delete form submission
    $('.ccp3o1-delete-form').on('submit', function(e) {
        var formData = $(this).serialize();
        console.log('Delete form submitted with data:', formData);
    });
});
