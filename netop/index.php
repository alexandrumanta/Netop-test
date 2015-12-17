<?php
require("blocks/header.php");
?>
	<div class="container" style="height: 100%">
		<div class="row">
			<div class="col-xs-6 center">
				<div class="wrapper">
					<div class="wrapper-container">
						<div class="search-filter">
							<div class="search-filter-container">
								<label class="search-filter-label">
									<i class="glyphicon glyphicon-search"></i>
								</label>
								<input type="text" class="search-filter-input" placeholder="Cauta cartea..." list="datalist" value="" />
								<span class="search-filter-cancel">
									<i class="glyphicon glyphicon-remove-sign"></i>
								</span>
							</div>
							<ul id="results"></ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
