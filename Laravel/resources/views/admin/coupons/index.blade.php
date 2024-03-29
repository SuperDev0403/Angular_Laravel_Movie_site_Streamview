@extends('layouts.admin')
@section('title',tr('coupons'))
@section('content-header',tr('coupons'))
@section('breadcrumb')
	<li><a href="{{route('admin.dashboard')}}"><i class="fa fa-dashboard"></i>{{tr('home')}}</a></li>
	<li class="active">{{tr('coupons')}}</li>
@endsection
@section('content')
	@include('notification.notify')
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-info">
				<div class="box-header label-primary">
					<b style="font-size: 18px;">{{tr('coupons')}}</b>
					<a href="{{route('admin.add.coupons')}}" class="btn btn-default pull-right">{{tr('add_coupon')}}</a>
				</div>
				<div class="box-body">
					@if(count($coupons) > 0)
						<table id="datatable-withoutpagination" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>{{tr('id')}}</th>
									<th>{{tr('title')}}</th>
									<th>{{tr('coupon_code')}}</th>
									<th>{{tr('amount_type')}}</th>
									<th>{{tr('amount')}}</th>
									<th>{{tr('expiry_date')}}</th>
									<th>{{tr('status')}}</th>
									<th>{{tr('action')}}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($coupons as $i=>$value)
								<tr>
									<td>{{showEntries($_GET, $i+1)}}</td>
									<td><a href="{{route('admin.coupon.view',$value->id)}}">{{$value->title}}</a></td>
									<td>{{$value->coupon_code}}</td>
									<td>
										@if($value->amount_type == 0)
										<span class="label label-primary">{{tr('percentage')}}</span>
										@else
										<span class="label label-primary">{{tr('absoulte')}}</span>
										@endif
									</td>
									<td>
										@if($value->amount_type == 0)
										{{$value->amount}} %
										@else
										{{Setting::get('currency')}} {{$value->amount}} 
										@endif
									</td>
									<td>
										{{date('d M y', strtotime($value->expiry_date))}}
									</td>
									<td>
										@if($value->status ==0)
										<span class="label label-warning">{{tr('declined')}}</span>
										@else
										<span class="label label-success">{{tr('approved')}}</span>
										@endif
									</td>
									<td>
										<ul class="admin-action btn btn-default">
											<li class="dropdown">
												<a class="dropdown-toggle" data-toggle="dropdown" href="#">
	                                                  {{tr('action')}} <span class="caret"></span>
	                                            </a>
											<ul class="dropdown-menu dropdown-menu-right">
												<li role="presentation">
													<a class = "menuitem"  tabindex= "-1" href="{{route('admin.edit.coupons',$value->id)}}">{{tr('edit')}}</a>
												</li>
												<li role="presentation">
													<a class="menuitem" tabindex="-1" href="{{route('admin.coupon.view',$value->id)}}">{{tr('view')}}</a>
												</li>
												<li role="presentation">
													<a class="menuitem" tabindex="-1" href="{{route('admin.delete.coupon',$value->id)}}" onclick="return confirm('Are You Sure?')">{{tr('delete')}}</a>
												</li>
												<li role="presentation">
													@if($value->status == 0)
													<a class="menuitem" tabindex="-1" href="{{route('admin.coupon.status',['id'=>$value->id,'status'=>1])}}" onclick="return confirm('Are You Sure?')">{{tr('approve')}} </a>
													@else
													<a class="menuitem" tabindex="-1" href="{{route('admin.coupon.status',['id'=>$value->id,'status'=>0])}}" onclick="return confirm('Are You Sure')">{{tr('decline')}}</a>
													@endif
												</li>
											</ul>
											</li>
										</ul>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<div align="right" id="paglink"><?php echo $coupons->links(); ?></div>
					@else
						<h3 class="no-result">{{tr('coupon_result_not_found_error')}}</h3>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection
