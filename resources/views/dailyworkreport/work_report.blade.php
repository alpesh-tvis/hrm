@extends('admin.master')

@section('content')
	<div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Work Report</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Work Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
    	<div class="container-fluid">
    		<div class="row">
    			<div class="col-md-12">
    				<table id="work_report" class="table table-bordered table-striped">
    					<thead>
                    		<tr>
                        		<th>Check In Time</th>
                        		<th>Check Out Time</th>
		                    </tr>
                		</thead>
                			<tr>
                				<td></td>
                				<td></td>
                			</tr>	
                		<tbody>
                			<tfoot>
                				<tr>
                        			<th>Check In Time</th>
                        			<th>Check Out Time</th>
		                    </tr>
                			</tfoot>	
                		</tbody>	
    				</table>	
    			</div>	
    		</div>	
    	</div>	
    </section>	
@endsection