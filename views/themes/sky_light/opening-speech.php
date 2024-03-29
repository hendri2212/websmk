<!-- CONTENT -->
<div class="bg-white p-3 my-2 ">
	<!-- <h5 class="page-title mb-3">Sambutan <?=__session('_headmaster')?></h5> -->
	<h5 class="border-start border-5 border-warning px-2 mb-3">Sambutan <?=__session('_headmaster')?></h5>
	<!-- <div class="card rounded-0 border border-secondary mb-3"> -->
	<div class="card border-0">
		<div class="card-body p-0">
			<p class="card-text"><?=get_opening_speech()?></p>
		</div>
	</div>
</div>
<div class="bg-white p-3 my-2 ">
	<!-- Get Random Posts -->
	<?php $query = get_random_posts(5); if ($query->num_rows() > 0) { ?>
		<h5 class="border-start border-5 border-warning px-2 mb-3">Baca Juga</h5>
		<?php foreach($query->result() as $row) { ?>
			<div class="card rounded-0 border-0 border-bottom rounded-0 mb-3">
				<div class="card-body p-0">
					<h5 class="card-title mb-0"><a href="<?=site_url('read/'.$row->id.'/'.$row->post_slug)?>" class="text-decoration-none"><?=$row->post_title?></a></h5>
					<p class="card-text"><?=substr(strip_tags($row->post_content), 0, 85)?></p>
					<!-- <div class="d-flex justify-content-between align-items-center mt-1">
						<small class="text-muted"><?=date('d/m/Y H:i', strtotime($row->created_at))?> WIB - <?=$row->post_author?></small>
						<a href="<?=site_url('read/'.$row->id.'/'.$row->post_slug)?>" class="btn btn-sm action-button rounded-0"><i class="fa fa-search"></i></a>
					</div> -->
				</div>
			</div>
		<?php } ?>
	<?php } ?>
</div>
<?php //$this->load->view('themes/sky_light/sidebar')?>
