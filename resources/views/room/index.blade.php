@extends('layouts.master-blue')
@section('title',isset($title) ? $title : config('app.name','LabRMS'))
@section('style')
{{ HTML::style(asset('css/select.bootstrap.min.css')) }}
<link rel="stylesheet" href="{{ asset('css/style.css') }}" />
<style>
	#page-body,#edit,#delete{
		display: none;
	}

	.panel {
		padding: 30px;
	}
</style>
@endsection
@section('content')
@include('modal.room.create')
<div class="container-fluid" id="page-body">
	<div class="col-md-12" id="room-info">
		<div class="panel panel-body table-responsive">
			<legend><h3 class="text-muted">Laboratory Room</h3></legend>
			<p class="text-muted">Note: Other actions will be shown when a row has been selected</p>
			{{-- <p class="text-muted">Deleted rooms can be restored {{ HTML::link('room/view/restore','here') }}</p> --}}
			<table class="table table-hover table-condensed table-bordered table-striped" id="roomTable">
				<thead>
					<th>ID</th>
					<th>Name</th>
					<th>Category</th>
					<th>Description</th>
					<th class="no-sort col-sm-1"></th>
				</thead>
			</table>
		</div>
	</div>
</div>
@endsection
@section('script')
{{ HTML::script(asset('js/dataTables.select.min.js')) }}
<script type="text/javascript">
	$(document).ready(function() {
		var table = $('#roomTable').DataTable( {
	  		select: {
	  			style: 'single'
	  		},
		    language: {
		        searchPlaceholder: "Search..."
		    },
	    	columnDefs:[
				{ targets: 'no-sort', orderable: false },
	    	],
	    	"dom": "<'row'<'col-sm-9'<'toolbar'>><'col-sm-3'f>>" +
						    "<'row'<'col-sm-12'tr>>" +
						    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
			"processing": true,
	        ajax: "{{ url('room') }}",
	        columns: [
	            { data: "id" },
	            { data: "name" },
	            { data: "category" },
	            { data: "description" },
	            { data: function(callback){
	            	return `
	            		<a href="` + '{{ url('room') }}' + '/' + callback.id +`" class="btn btn-default btn-sm btn-block">View</a>
	            	`;
	            } }
	        ],
	    } );

	 	$("div.toolbar").html(`
 			<button type="button" id="new" class="btn btn-primary btn-flat" style="margin-right:5px;padding: 5px 10px;" data-toggle="modal" data-target="#createRoomModal"><span class="glyphicon glyphicon-plus"></span>  Add</button>
 			<button id="edit" class="btn btn-default btn-flat" style="margin-right:5px;padding: 6px 10px;"><span class="glyphicon glyphicon-pencil"></span>  Update</button>
 			<button id="delete" class="btn btn-danger btn-flat" style="margin-right:5px;padding: 5px 10px;"><span class="glyphicon glyphicon-trash"></span> Remove</button>
		`);

    table
        .on( 'select', function ( e, dt, type, indexes ) {
            // var rowData = table.rows( indexes ).data().toArray();
            // events.prepend( '<div><b>'+type+' selection</b> - '+JSON.stringify( rowData )+'</div>' );
            $('#edit').show()
            $('#delete').show()
        } )
        .on( 'deselect', function ( e, dt, type, indexes ) {
            // var rowData = table.rows( indexes ).data().toArray();
            // events.prepend( '<div><b>'+type+' <i>de</i>selection</b> - '+JSON.stringify( rowData )+'</div>' );
            $('#edit').hide()
            $('#delete').hide()
        } );

		$('#edit').on('click',function(){
			try{
				if(table.row('.selected').data().id != null && table.row('.selected').data().id  && table.row('.selected').data().id >= 0)
				{
					window.location.href = "{{ url('room') }}" + '/' + table.row('.selected').data().id + '/edit'
					// $('#edit-id').val(table.row('.selected').data().id)
					// $('#edit-name').val(table.row('.selected').data().name)
					// $('#edit-description').val(table.row('.selected').data().description)
					// $('#updateRoomModal').modal('show');
				}
			}catch( error ){
				swal('Oops..','You must choose atleast 1 row','error');
			}
		});

	    $('#delete').on('click',function(){
			try{
				if(table.row('.selected').data().id != null && table.row('.selected').data().id  && table.row('.selected').data().id >= 0)
				{
			        swal({
			          title: "Are you sure?",
			          text: "This room will be removed from database?",
			          type: "warning",
			          showCancelButton: true,
			          confirmButtonText: "Yes, delete it!",
			          cancelButtonText: "No, cancel it!",
			          closeOnConfirm: false,
			          closeOnCancel: false
			        },
			        function(isConfirm){
			          if (isConfirm) {
     					$.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
							type: 'delete',
							url: '{{ url("room/") }}' + "/" + table.row('.selected').data().id,
							data: {
								'id': table.row('.selected').data().id
							},
							dataType: 'json',
							success: function(response){
								if(response == 'success'){
									swal('Operation Successful','Room removed from database','success')
					        		table.row('.selected').remove().draw( false );
					        	}else{
									swal('Operation Unsuccessful','Error occurred while deleting a record','error')
								}
					            $('#edit').hide()
					            $('#delete').hide()
							},
							error: function(){
								swal('Operation Unsuccessful','Error occurred while deleting a record','error')
							}
						});
			          } else {
			            swal("Cancelled", "Operation Cancelled", "error");
			          }
			        })
				}
			}catch( error ){
				swal('Oops..','You must choose atleast 1 row','error');
			}
	    });

	    $('#table tbody').on( 'click', 'tr', function () {
	      if ( $(this).hasClass('selected') ) {
	          $(this).removeClass('selected');
	      }
	      else {
	          table.$('tr.selected').removeClass('selected');
	          $(this).addClass('selected');
	      }
	    } );
	} );
</script>
@stop
