$(document).ready(function(){
    $("#search_character").keyup(function(){
 
        // Retrieve the input field text and reset the count to zero
        var input = $(this).val(); 
	var count = 0;
 
        // Loop through the list 	*!* replace OL by invisible list
        $(".characterlist li").each(function(){
 
            // If the list item does not contain the text phrase fade it out
            if ($(this).text().search(new RegExp(input, "i")) < 0) {
                $(this).fadeOut();
 
            // Show the list item if the phrase matches and increase the count by 1
            } else {
                $(this).show();
                count++;
            }
        });
 
        // Update Character ID if the count is only one
        if (count == 1) ;
        $("#pid").text( "9999" ); // *!* Find how to get associated PID...
    });
});