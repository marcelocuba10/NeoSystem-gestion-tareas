@extends('user::layouts.adminLTE.app')
@section('content')

<section class="section">
    <div class="container-fluid">
      <!-- ========== title-wrapper start ========== -->
      <div class="title-wrapper pt-30">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="title mb-30">
              <h2>Bienvenido a {{ config('app.name') }}</h2>
            </div>
          </div>
        </div>
      </div>
      <!-- ========== title-wrapper end ========== -->

      <div class="row">
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon purple">
              <i class="lni lni-users"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Total Clientes</h6>
              <h3 class="text-bold mb-10">{{ $cant_customers }}</h3>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon orange">
              <i class="lni lni-grid-alt"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Total Productos</h6>
              <h3 class="text-bold mb-10">{{ $cant_products }}</h3>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon success">
              <i class="lni lni-credit-cards"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Total Ventas</h6>
              <h3 class="text-bold mb-10">G$ {{number_format($total_sales, 0)}}</h3>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-sm-6">
          <div class="icon-card mb-30">
            <div class="icon primary">
              <i class="lni lni-hand"></i>
            </div>
            <div class="content">
              <h6 class="mb-10">Total Visitas</h6>
              <h3 class="text-bold mb-10">{{ $total_visits }}</h3>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Customer visits -->
        <div class="col-lg-6 col-xl-6 col-xxl-6">
          <div class="card-style clients-table-card mb-30">
            <div class="title d-flex justify-content-between align-items-center">
              <h6 class="mb-10">Visita Clientes</h6>
              <div class="more-btn-wrapper mb-10">
                <button class="more-btn dropdown-toggle" id="moreAction" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="lni lni-more-alt"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="moreAction">
                  <li class="dropdown-item">
                    <a href="#0" class="text-gray">Add All</a>
                  </li>
                  <li class="dropdown-item">
                    <a href="#0" class="text-gray">Remove All</a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="table-wrapper table-responsive">
              <table class="table">
                <tbody>
                  @foreach ($customer_visits as $customer_visit)
                    <tr>
                      <td>
                        <div class="employee-image">
                          <img src="{{ asset('/public/images/user-icon-business-man-flat-png-transparent.png') }}" alt="">
                        </div>
                      </td>
                      <td class="employee-info">
                        <h5 class="text-medium">{{ $customer_visit->customer_name }}</h5>
                        <p><i class="lni lni-phone"></i>&nbsp;{{ $customer_visit->phone }} / {{ $customer_visit->estate }}</p>
                      </td>
                      <td>
                        <div class="d-flex justify-content-end">
                          <button class="status-btn close-btn border-0 m-1">
                            Cancel
                          </button>
                          <button class="status-btn primary-btn border-0 m-1">
                            Add
                          </button>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Appointments -->
        <div class="col-lg-6 col-xl-6 col-xxl-6">
          <div class="card-style mb-30">
            <div class="title mb-10 d-flex justify-content-between align-items-center">
              <h6 class="mb-10">Agenda de Visitas y Llamadas</h6>
              <div class="more-btn-wrapper">
                <button class="more-btn dropdown-toggle" id="moreAction" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="lni lni-more-alt"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="moreAction">
                  <li class="dropdown-item">
                    <a href="#0" class="text-gray">Mark as Read</a>
                  </li>
                  <li class="dropdown-item">
                    <a href="#0" class="text-gray">Reply</a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="todo-list-wrapper">
              <ul>
                <li class="todo-list-item success">
                  <div class="todo-content">
                    <p class="text-sm mb-2">
                      <i class="lni lni-calendar"></i>
                      14 February,2024
                    </p>
                    <h5 class="text-bold mb-10">Uideck Yearly Meetings</h5>
                    <p class="text-sm">
                      <i class="lni lni-alarm-clock"></i>
                      10:20 AM - 3:00 PM
                    </p>
                  </div>
                  <div class="todo-status">
                    <span class="status-btn success-btn">Completed</span>
                  </div>
                </li>
                <li class="todo-list-item primary">
                  <div class="todo-content">
                    <p class="text-sm mb-2">
                      <i class="lni lni-calendar"></i>
                      14 February,2024
                    </p>
                    <h5 class="text-bold mb-10">2024 Dribbble Meet Up</h5>
                    <p class="text-sm">
                      <i class="lni lni-alarm-clock"></i>
                      10:20 AM - 3:00 PM
                    </p>
                  </div>
                  <div class="todo-status">
                    <span class="status-btn active-btn">Upcoming</span>
                  </div>
                </li>
                <li class="todo-list-item orange">
                  <div class="todo-content">
                    <p class="text-sm mb-2">
                      <i class="lni lni-calendar"></i>
                      14 February,2024
                    </p>
                    <h5 class="text-bold mb-10">
                      Plain Admin Dashboard Meeting
                    </h5>
                    <p class="text-sm">
                      <i class="lni lni-alarm-clock"></i>
                      10:20 AM - 3:00 PM
                    </p>
                  </div>
                  <div class="todo-status">
                    <span class="status-btn orange-btn">Pending</span>
                  </div>
                </li>
                <li class="todo-list-item danger">
                  <div class="todo-content">
                    <p class="text-sm mb-2">
                      <i class="lni lni-calendar"></i>
                      14 February,2024
                    </p>
                    <h5 class="text-bold mb-10">Uideck Yearly Meetings</h5>
                    <p class="text-sm">
                      <i class="lni lni-alarm-clock"></i>
                      10:20 AM - 3:00 PM
                    </p>
                  </div>
                  <div class="todo-status">
                    <span class="status-btn close-btn">Canceled</span>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <!-- End Cart -->
        </div>
      </div>
    </div>
</section>
@endsection