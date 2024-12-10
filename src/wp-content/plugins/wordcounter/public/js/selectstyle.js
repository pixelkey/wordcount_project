
document.addEventListener("DOMContentLoaded", function () {
    var allowedStyles = ['bluesky', 'blackgrey', 'blackwhite', 'blackneon'];

    // Retrieve the current style from localStorage or default to 'bluesky'
    var currentStyle = localStorage.getItem('selected_style') || 'bluesky';

    // Function to capitalize the first letter
    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Generate the form dynamically
    var output = '<form class="style-selector-form">';
    allowedStyles.forEach(function (style) {
        var checked = (currentStyle === style) ? 'checked' : '';
        var styleName = ucfirst(style);
        output += '<div class="button"><label><input type="radio" name="selected_style" value="' + style + '" ' + checked + '> ' + styleName + '</label><br></div>';
    });
    output += '</form>';

    // Insert the form into the page
    var container = document.getElementById('style-selector-container');
    if (container) {
        container.innerHTML = output;
    } else {
        console.error('Container for style selector not found.');
        return;
    }

    // Function to apply the selected style
    function applyStyle(style) {
        var linkId = 'dynamic-style';
        var oldLink = document.getElementById(linkId);
        if (oldLink) {
            oldLink.parentNode.removeChild(oldLink);
        }

        var link = document.createElement('link');
        link.id = linkId;
        link.rel = 'stylesheet';
        link.type = 'text/css';
        link.href = styleSelectorData.pluginBaseUrl + 'public/scss/' + style + '.css';
        link.media = 'all';
        document.getElementsByTagName('head')[0].appendChild(link);
    }

    // Apply the current style on page load
    applyStyle(currentStyle);

    // Handle style change events
    var radios = document.querySelectorAll(".style-selector-form input[name='selected_style']");
    radios.forEach(function (radio) {
        radio.addEventListener("change", function () {
            var selectedStyle = this.value;
            localStorage.setItem('selected_style', selectedStyle);
            applyStyle(selectedStyle);
        });
    });
});

// function select_style() {
//     $allowed_styles = array('bluesky', 'blackgrey', 'blackwhite', 'blackneon');

//     if (isset($_POST['selected_style']) && in_array($_POST['selected_style'], $allowed_styles)) {
//         $current_style = $_POST['selected_style'];
//     } else {
//         $current_style = 'bluesky';
//     }

//     $output = '<form method="post" class="style-selector-form">';
//     $styles = array('bluesky', 'blackgrey', 'blackwhite', 'blackneon');
//     foreach($styles as $style) {
//         $checked = ($current_style == $style) ? 'checked' : '';
//         $style_name = ucfirst($style); // Capitalize the first letter
//         $output.= '<div class="button"><label><input type="radio" name="selected_style" value="'.$style. '" '.$checked. '> '.$style_name. '</label><br></div>';
//     }


//     $output.= '</form>';
//     $output.= '
//         < script type = "text/javascript" >
//             document.addEventListener("DOMContentLoaded", function () {
//                 var radios = document.querySelectorAll(".style-selector-form input[name=\'selected_style\']");
//                 radios.forEach(function (radio) {
//                     radio.addEventListener("change", function () {
//                         this.form.submit();
//                     });
//                 });
//             });
//     </script >
//         ';

//     return $output;
// }
