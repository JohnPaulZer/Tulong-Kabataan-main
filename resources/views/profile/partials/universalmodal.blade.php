<div id="toast"
    style="position:fixed; top:30px; left:50%; transform:translateX(-50%);
            background:#16a34a; color:#fff; padding:12px 20px; border-radius:8px;
            box-shadow:0 2px 8px rgba(0,0,0,0.2); font-size:14px; font-weight:500;
            opacity:0; pointer-events:none; transition:opacity 0.5s ease;
            z-index:10000;">
</div>

<div id="confirmModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); justify-content:center; align-items:center;
            z-index:9999;">

    <div
        style="background:#fff; padding:20px; border-radius:8px; width:350px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,0.2);">

        <h3 id="confirmModalTitle" style="margin-bottom:10px; font-size:18px; font-weight:600; color:#1f2937;">
            Confirm Action
        </h3>
        <p id="confirmModalMessage" style="color:#374151; font-size:14px; margin-bottom:20px;">
            Are you sure you want to continue?
        </p>

        <div style="display:flex; justify-content:center; gap:12px;">
            <button id="cancelConfirmBtn"
                style="padding:8px 16px; background:#f3f4f6; border:none; border-radius:6px; cursor:pointer;">
                Cancel
            </button>
            <button id="confirmActionBtn"
                style="padding:8px 16px; background:#ef4444; color:#fff; border:none; border-radius:6px; cursor:pointer;">
                Confirm
            </button>
        </div>
    </div>
</div>
