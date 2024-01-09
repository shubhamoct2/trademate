@if( $assigned_to == 'developer' && $status=='open')

<span class="site-badge success">
   <!-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
      <path d="m9 11 3 3L22 4" />
   </svg> -->
   <span>
      {{ __('Assigned to developer') }}
   </span>
</span>
@endif

@if( $assigned_to == 'admin' && $status=='open')
<a href="{{ route('admin.ticket.assign',$uuid) }}" class="site-btn-xs red-btn table-btn py-2 px-3 lh-base" data-bs-toggle="tooltip" title="Assign Ticket to Developer" data-bs-original-title="Assign Ticket to Developer">
   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="user-plus" icon-name="user-plus" class="lucide lucide-user-plus">
      <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
      <circle cx="9" cy="7" r="4"></circle>
      <line x1="19" x2="19" y1="8" y2="14"></line>
      <line x1="22" x2="16" y1="11" y2="11"></line>
   </svg>
   {{ __('Developer') }}
</a>
@endif