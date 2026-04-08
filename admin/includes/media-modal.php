<?php
// Media picker modal — layout styled in admin.css (#mediaLibraryModal)
?>
<!-- Media Library Modal -->
<div id="mediaLibraryModal" aria-hidden="true">
  <div class="media-modal-container">
    <div class="media-modal-header">
      <h2>Select or Upload Media</h2>
      <button type="button" id="closeMediaModal" aria-label="Close">&times;</button>
    </div>
    <div class="media-modal-tabs">
      <button type="button" class="media-tab active" data-tab="library">Media Library</button>
      <button type="button" class="media-tab" data-tab="upload">Upload New</button>
    </div>
    <div class="media-modal-body">
      <div id="mediaLibraryTab" class="media-tab-content active">
        <div style="margin-bottom: 20px;">
          <input type="text" id="mediaSearchInput" placeholder="Search media…" autocomplete="off">
        </div>
        <p id="mediaMultiSelectHint" class="form-help" style="display:none;margin:0 0 12px 0;font-size:13px;color:var(--text-muted);">Click an image to select it. <strong>Ctrl</strong> or <strong>⌘</strong>+click to add or remove. <strong>Shift</strong>+click to select a range.</p>
        <div id="mediaGridContainer"></div>
      </div>
      <div id="mediaUploadTab" class="media-tab-content" style="display:none;">
        <form id="mediaUploadForm" method="post" action="#" enctype="multipart/form-data" novalidate>
          <div style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 40px; text-align: center; margin-bottom: 20px; background: var(--surface-elevated);">
            <label for="mediaFileInput" style="display:inline-block;padding:10px 20px;background:var(--primary-color);color:#fff;border-radius:6px;cursor:pointer;font-weight:500;">Select Files</label>
            <input type="file" id="mediaFileInput" name="file" accept="image/jpeg,image/jpg,image/png,image/webp,.jpg,.jpeg,.png,.webp" multiple style="display:none;">
            <p id="selectedFilesInfo" style="margin: 15px 0 0 0; color: var(--text-color); font-weight: 500; display: none;"></p>
            <p style="margin: 12px 0 0 0; color: var(--text-muted); font-size: 12px;">JPEG, PNG, or WebP</p>
          </div>
          <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button type="button" onclick="switchMediaTab('library')" class="btn btn-outline">Cancel</button>
            <button type="submit" id="uploadSubmitBtn" class="btn btn-primary">Upload</button>
          </div>
        </form>
      </div>
    </div>
    <div class="media-modal-footer">
      <button type="button" id="cancelMediaSelection" class="btn btn-outline">Cancel</button>
      <button type="button" id="insertMediaBtn" class="btn btn-primary" style="display:none;">Insert Selected (<span id="insertCount">0</span>)</button>
    </div>
  </div>
</div>
