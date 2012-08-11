<!-- header -->
<?php require 'header.php' ?>

<!-- section -->
<section>
	<div class="row">
		<div class="span10 offset1">
			<?php if( ! empty( $this->data['errors'] ) ): ?>
				<div class="alert alert-info">
					Woops, please fix the following oopsies:
					<ul>
						<?php foreach( $this->data['errors'] as $error ) : ?>
								<li><?php echo $error; ?></li>
						<?php endforeach; ?>
					</ul>
					cheerios ;)
				</div>
			<?php endif; ?>
			<form class="well form-search">
				<p>
						<input name="jsfiddlelink" type="text" class="input-large search-query" placeholder="enter the URL for your JSFiddle you want to download" style="width:100%;">
				</p>
				<p>
						<button name="type" value="zip" type="submit" class="btn">Download as <strong>zip</strong></button>
						<button name="type" value="tar" type="submit" class="btn">Download as <strong>tar.gz</strong></button>
				</p>
			</form>
		</div>
	</div>
</section>

<!-- footer -->
<?php require 'footer.php' ?>
