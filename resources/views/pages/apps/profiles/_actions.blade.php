<a href="{{ route('profiles.show', $profile->uuid) }}" class="text-blue-500 m-2" title="View ">
<span class="fas fa-eye" aria-hidden="true"></span>
    </a>
    @if ($profile->status === 'ACTIVE') 

                    <a href="#" class="text-danger update-status m-2" data-id="{{$profile->uuid }}"    data-status="INACTIVE" 
       data-action="Deactivate"  title="Deactivate">
                        <span class="fas fa-ban" aria-hidden="true"></span>
    </a>
  <a href="#" class="text-warning m-2 reset-password"  data-id="{{$profile->uuid }}"   title="Reset">
        <span class="fas fa-redo" aria-hidden="true"></span>
    </a>


            @else 
              
            <a href="#" class="text-success  update-status m-2" data-id="{{$profile->uuid }}"    data-status="ACTIVE" 
       data-action="Activate"  title="Activate account">
                        <span class="fas fa-check" aria-hidden="true"></span>
    </a>
                
            @endif 
