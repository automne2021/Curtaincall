<?php
// filepath: c:\Users\VY\Downloads\curtaincall\views\layouts\footer.php
?>
<button onclick="topFunction()" id="myBtn" title="Go to top">
    <i class="bi bi-chevron-double-up"></i>
</button>
<script>
    //Get the button
    var mybutton = document.getElementById("myBtn");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
</script>
<footer>
    <p>&copy; <?php echo date("Y"); ?> CurtainCall. All Rights Reserved.</p>
</footer>

<?php include 'views/auth/login-modal.php'; ?>
<?php include 'views/auth/register-modal.php'; ?>

</body>

</html>