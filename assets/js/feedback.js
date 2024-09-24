const fullStarRatingsContainer = $(".full-star-ratings");

        if (fullStarRatingsContainer) {
            fullStarRatingsContainer.rateYo({
                starWidth: "26px",
                spacing: "6px",
                // rating: 0
            });
        }

        $('#platformFeedbackForm').submit(function(event) {
            // Prevent default form submission
            event.preventDefault();

            // console.log(jsonData);

            let isAjaxInProgress = false;

            document.getElementById('platformFeedbackSubBtn').setAttribute("disabled", "disabled");
            showSpinner();
            $.ajax({
                type: 'POST',
                url: '/api/process_feedback',
                data: {
                    platformFeedback: {
                        rating: $('#platformRating').rateYo('rating'),
                        feedback: document.getElementById('platformFeedback').value,
                        improvement: document.getElementById('platformImprovements').value,
                    }
                },
                dataType: 'json',
                beforeSend: function() {
                    if (isAjaxInProgress) {
                        return false;
                    }
                    isAjaxInProgress = true;
                },
                success: function(response) {
                    console.log(response);
                    isAjaxInProgress = false;
                    document.getElementById('platformFeedbackSubBtn').removeAttribute("disabled");
                    hideSpinner();
                    if (!response.status) {
                        // if (response.error === "Invalid feedback"){
                        // }else if (response.error === "Sql Error"){
                        // }
                    } else {
                        showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Thank You', 'Thank you for your feedback');
                    }
                },
                error: function(xhr, status, error) {
                    // Reset the flag on error to allow making another AJAX call
                    isAjaxInProgress = false;
                    // Handle error
                    document.getElementById('platformFeedbackSubBtn').removeAttribute("disabled");
                    hideSpinner();
                    // console.error("Error: " + error);
                }
            });
        });

        $('#feedbackForm').submit(function(event) {
            // Prevent default form submission
            event.preventDefault();

            // Define an empty array to store ratings and feedback
            let data = [];

            // Iterate over each card element
            $('.ratingContainer').each(function() {
                let challengeId = $(this).find('.full-star-ratings').attr('id').replace('ratting', '');

                let rating = $('#' + $(this).find('.full-star-ratings').attr('id')).rateYo('rating');

                let feedback = $(this).find('textarea').val();

                let entry = {
                    id: challengeId,
                    rating: rating,
                    feedback: feedback
                };

                data.push(entry);
            });

            const ratingData = {
                "ratingData": JSON.parse(JSON.stringify(data))
            };

            // console.log(jsonData);

            let isAjaxInProgress = false;

            document.getElementsByClassName('btn-primary')[0].setAttribute("disabled", "disabled");
            showSpinner();
            $.ajax({
                type: 'POST',
                url: '/api/process_feedback',
                data: ratingData,
                dataType: 'json',
                beforeSend: function() {
                    if (isAjaxInProgress) {
                        return false;
                    }
                    isAjaxInProgress = true;
                },
                success: function(response) {
                    // console.log(response);
                    isAjaxInProgress = false;
                    document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
                    hideSpinner();
                    if (!response.status) {
                        // if (response.error === "Invalid feedback"){
                        // }else if (response.error === "Sql Error"){
                        // }
                    } else {
                        showToast(5000, 'mdi-check-circle', 'animate__shakeX', 'text-success', 'Thank You', 'Thank you for your feedback');
                    }
                },
                error: function(xhr, status, error) {
                    // Reset the flag on error to allow making another AJAX call
                    isAjaxInProgress = false;
                    // Handle error
                    document.getElementsByClassName('btn-primary')[0].removeAttribute("disabled");
                    hideSpinner();
                    // console.error("Error: " + error);
                }
            });
        });