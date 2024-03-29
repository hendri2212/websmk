<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<script type="text/javascript">
var page = 1;
var total_page = "<?=$total_page;?>";
$(document).ready(function() {
	if (parseInt(total_page) == page || parseInt(total_page) == 0) {
		$('.more-alumni').remove();
	}
});

function get_alumni() {
	page++;
	var data = {
		page_number: page
	};
	if ( page <= parseInt(total_page) ) {
		_H.Loading( true );
		$.post( _BASE_URL + 'public/alumni_directory/get_alumni', data, function( response ) {
			_H.Loading( false );
			var res = _H.StrToObject( response );
			var rows = res.rows;
			var str = '';
			var no = parseInt($('.number:last').text()) + 1;
			for (var z in rows) {
				var row = rows[ z ];
				str += '<div class="col-md-6 mb-4 profile-alumni">';
				str += '<div class="card h-100 border border-secondary rounded-0">';
				str += '<div class="row no-gutters">';
				str += '<div class="col-md-4">';
				str += '<img src="' + row.photo + '" class="card-img border border-secondary rounded-0 m-2">';
				str += '</div>';
				str += '<div class="col-md-8">';
				str += '<div class="card-body pt-2 pb-2">';
				str += '<dl class="row">';
				str += '<dt class="col-sm-5">Nama Lengkap</dt>';
				str += '<dd class="col-sm-7">' + row.full_name + '</dd>';
				//
				str += '<dt class="col-sm-5">' + _IDENTITY_NUMBER + '</dt>';
				str += '<dd class="col-sm-7">' + row.identity_number + '</dd>';
				//
				str += '<dt class="col-sm-5">Jenis Kelamin</dt>';
				str += '<dd class="col-sm-7">' + row.gender + '</dd>';
				//
				str += '<dt class="col-sm-5">Tempat Lahir</dt>';
				str += '<dd class="col-sm-7">' + row.birth_place + '</dd>';
				//
				str += '<dt class="col-sm-5">Tanggal Lahir</dt>';
				str += '<dd class="col-sm-7">' + row.birth_date + '</dd>';
				//
				str += '<dt class="col-sm-5">Tahum Masuk</dt>';
				str += '<dd class="col-sm-7">' + row.start_date + '</dd>';
				//
				str += '<dt class="col-sm-5">Tahum Keluar</dt>';
				str += '<dd class="col-sm-7">' + row.end_date + '</dd>';
				str += '</dl>';
				str += '</div>';
				str += '</div>';
				str += '</div>';
				str += '</div>';
				str += '</div>';
			}
			var elementId = $("div.profile-alumni:last");
			$( str ).insertAfter( elementId );
			if ( page == parseInt(total_page) ) $('.more-alumni').remove();
		});
	}
}
</script>
<!-- <h5 class="page-title mb-3"><?=$page_title?></h5> -->
<div class="bg-white p-3 my-2">
	<div class="d-flex mb-3">
		<input type="text" name="" class="form-control form-control-sm" placeholder="Type search here">
		<button class="btn btn-sm btn-outline-success" style="margin-left: 10px">Login</button>
	</div>
	<?php foreach($query->result() as $row) { ?>
		<div class="alert alert-success d-flex" role="alert">
			<?php
			$photo = 'no-image.jpg';
			if ($row->photo && file_exists($_SERVER['DOCUMENT_ROOT'] . '/media_library/students/'.$row->photo)) {
				$photo = $row->photo;
			}
			// echo '<img src="' . base_url('media_library/students/'.$photo).'" class="card-img border border-secondary rounded-0 m-2">';
			echo '<img src="' . base_url('assets/img/person.png').'" width="60px">';
			?>
			<div class="mx-3 d-flex flex-column">
				<label class="fw-bold"><?=$row->full_name?></label>
				<label><?=$row->reason?></label>
				<label><?=$row->street_address?></label>
			</div>
			<!-- <div class="col-md-8">
				<div class="card-body pt-2 pb-2">
					<dl class="row">
						<dt class="col-sm-5">Nama Lengkap</dt>
						<dd class="col-sm-7"><?=$row->full_name?></dd>

						<dt class="col-sm-5"><?=__session('_identity_number')?></dt>
						<dd class="col-sm-7"><?=$row->identity_number?></dd>

						<dt class="col-sm-5">Jenis Kelamin</dt>
						<dd class="col-sm-7"><?=$row->gender?></dd>

						<dt class="col-sm-5">Tempat Lahir</dt>
						<dd class="col-sm-7"><?=$row->birth_place?></dd>

						<dt class="col-sm-5">Tanggal Lahir</dt>
						<dd class="col-sm-7"><?=indo_date($row->birth_date)?></dd>

						<dt class="col-sm-5">Tahum Masuk</dt>
						<dd class="col-sm-7"><?=$row->start_date?></dd>

						<dt class="col-sm-5">Tahum Keluar</dt>
						<dd class="col-sm-7"><?=$row->end_date?></dd>
					</dl>
				</div>
			</div> -->
		</div>
	<?php } ?>
</div>
<div class="px-3 d-flex justify-content-end align-items-center mb-3 w-100 more-alumni">
	<button type="button" onclick="get_alumni()" class="btn btn-sm btn-outline-warning"><i class="fa fa-refresh"></i> Tampilkan Lebih Banyak</button>
</div>
