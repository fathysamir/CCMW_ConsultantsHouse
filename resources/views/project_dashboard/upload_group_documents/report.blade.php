@if(count($notes)>0)
<div class="row ml-1">
    <div style="display: flex; align-items: center;"><h5 style="color: rgb(25, 226, 25); margin: 0; display: flex; align-items: center; gap: 8px;"><b><span class="fe fe-24 fe-check"></span></b><label style="margin: 0;">Notes : </label></h5></div>
   
    <div  style="margin-left:4.4rem; !important">
        @foreach($notes as $note)
        <p>ــ {{ $note }}</p>
        @endforeach
    </div>
</div>
@endif
@if(count($mistakes)>0 && count($notes)>0)
<div class="form-group mt-4" style="display: flex; align-items: center;margin-bottom:3rem;">
    <hr style="flex: 1; margin: 0;border: 1px solid rgb(25, 98, 234);">
</div>
@endif
@if(count($mistakes)>0)
<div class="row ml-1">
    <div style="display: flex; align-items: center;"><h5 style="color: rgb(255, 53, 53); margin: 0; display: flex; align-items: center; gap: 8px;"><b><span class="fe fe-24 fe-x"></span></b><label style="margin: 0;">Mistakes : </label></h5></div>
    <div style="margin-left:2.5rem; !important">
        @foreach($mistakes as $mistake)
        <p>ــ {{ $mistake }}</p>
        @endforeach
        
    </div>
</div>
@endif