<!-- administrator/event/partials/eventmodal.blade.php -->
<div id="eventDetailsModal"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(17,24,39,0.6); justify-content:center; align-items:center;
            z-index:9999; backdrop-filter:blur(4px);">

  <div style="background:#fff; padding:24px; border-radius:16px; width:520px; max-height:90vh; overflow:auto;
              box-shadow:0 12px 40px rgba(0,0,0,0.2); animation:slideUp .3s ease;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
      <h2 id="modalEventTitle" style="font-size:20px; font-weight:800; color:#111827; margin:0;"></h2>
      <button id="closeEventModalBtn"
              style="border:none; background:transparent; color:#6b7280; font-size:20px; cursor:pointer;">
        <i class="ri-close-line"></i>
      </button>
    </div>

    <div style="border-radius:12px; overflow:hidden; margin-bottom:12px;">
      <img id="modalEventPhoto" src="" alt="Event Photo"
           style="width:100%; height:230px; object-fit:cover; display:none;">
    </div>

    <p id="modalEventDesc"
       style="font-size:15px; color:#374151; line-height:1.6; margin-bottom:20px;"></p>

    <div style="border-top:1px solid #e5e7eb; padding-top:16px; margin-top:12px;">
      <h4>Event Information</h4>
      <p><strong>Start Date:</strong> <span id="modalStartDate"></span></p>
      <p><strong>End Date:</strong> <span id="modalEndDate"></span></p>
      <p><strong>Deadline:</strong> <span id="modalDeadline"></span></p>
      <p><strong>Location:</strong> <span id="modalLocation"></span></p>
      <p><strong>Coordinates:</strong> <span id="modalCoords"></span></p>
    </div>

    <div style="border-top:1px solid #e5e7eb; padding-top:16px; margin-top:12px;">
      <h4>Coordinator</h4>
      <p><strong>Name:</strong> <span id="modalCoordName"></span></p>
      <p><strong>Email:</strong> <span id="modalCoordEmail"></span></p>
      <p><strong>Phone:</strong> <span id="modalCoordPhone"></span></p>
    </div>

    <div style="border-top:1px solid #e5e7eb; padding-top:16px; margin-top:12px;">
      <h4>Volunteers</h4>
      <p><strong>Total Registered:</strong> <span id="modalRegistered"></span></p>
    </div>

  </div>
</div>
