	<footer class="main-footer">
		<div class="footer-inner">
			<div class="footer-text">
				&copy; 2015 
				<strong>Library</strong> 
			</div>
			<div class="go-up">
				<a href="#" rel="go-top">
					<i class="fa fa-angle-up"></i>
				</a>
			</div>
		</div>
	</footer>
</div>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?php echo HOST;?>assets/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo HOST;?>assets/css/custom.css">

<script src="<?php echo HOST; ?>assets/js/jquery-1.11.3.min.js"></script>
<script src="<?php echo HOST;?>assets/js/bootstrap.min.js"></script>

<script type="text/javascript">
	$(document).ready(function($){
		setTimeout(function(){ $(".fade-in-effect").addClass('in'); }, 1);
	});
	var url = window.location;

	$('ul.pagination a[href="'+ url +'"]').parent().addClass('active');
	$('ul.pagination a').filter(function() {
		return this.href == url;
	}).parent().addClass('active');
</script>

</body>
</html>