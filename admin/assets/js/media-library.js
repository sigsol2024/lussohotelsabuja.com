/**
 * Media Library Modal JavaScript (copied from existing CMS)
 */

let mediaModalState = {
  targetInputId: null,
  targetPreviewId: null,
  selectedMedia: null,
  selectedMediaMultiple: [],
  allowMultiple: false,
  currentPage: 1,
  currentSearch: ''
};

function normalizeAdminBase() {
  const base = (typeof ADMIN_URL !== 'undefined' ? ADMIN_URL : (window.ADMIN_URL || '')) || '';
  return base.replace(/\/?$/, '/');
}

document.addEventListener('DOMContentLoaded', function() {
  const closeBtn = document.getElementById('closeMediaModal');
  const cancelBtn = document.getElementById('cancelMediaSelection');
  const modal = document.getElementById('mediaLibraryModal');

  if (closeBtn) closeBtn.addEventListener('click', closeMediaModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeMediaModal);
  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === modal) closeMediaModal();
    });
  }

  document.querySelectorAll('.media-tab').forEach(tab => {
    tab.addEventListener('click', function() {
      switchMediaTab(this.getAttribute('data-tab'));
    });
  });

  const searchInput = document.getElementById('mediaSearchInput');
  if (searchInput) {
    let t;
    searchInput.addEventListener('input', function(e) {
      clearTimeout(t);
      t = setTimeout(() => {
        mediaModalState.currentSearch = e.target.value;
        mediaModalState.currentPage = 1;
        loadMediaLibrary();
      }, 400);
    });
  }

  const fileInputEl = document.getElementById('mediaFileInput');
  if (fileInputEl) {
    fileInputEl.addEventListener('change', handleMediaFileSelect);
  }

  const insertBtn = document.getElementById('insertMediaBtn');
  if (insertBtn) insertBtn.addEventListener('click', insertSelectedMedia);
});

/* Capture phase: always intercept upload form submit (avoids accidental native GET submit or missed listeners) */
document.addEventListener('submit', function(e) {
  if (e.target && e.target.id === 'mediaUploadForm') {
    e.preventDefault();
    e.stopPropagation();
    uploadNewMedia();
  }
}, true);

function shouldAllowMultipleSelection(targetInputId) {
  const multipleFields = ['gallery_image_new', 'room_gallery_images', 'gallery_images'];
  return multipleFields.some(field => targetInputId.includes(field) || targetInputId === field);
}

function openMediaModal(targetInputId, targetPreviewId, allowMultiple = null) {
  mediaModalState.targetInputId = targetInputId;
  mediaModalState.targetPreviewId = targetPreviewId;
  mediaModalState.selectedMedia = null;
  mediaModalState.selectedMediaMultiple = [];
  mediaModalState.allowMultiple = allowMultiple === null ? shouldAllowMultipleSelection(targetInputId) : allowMultiple;

  const modal = document.getElementById('mediaLibraryModal');
  if (!modal) return;
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  switchMediaTab('library');
  loadMediaLibrary();
  updateInsertButton();
}

function closeMediaModal() {
  const modal = document.getElementById('mediaLibraryModal');
  if (!modal) return;
  modal.style.display = 'none';
  document.body.style.overflow = '';
  mediaModalState.selectedMedia = null;
  mediaModalState.selectedMediaMultiple = [];
  updateInsertButton();
}

function switchMediaTab(tabName) {
  document.querySelectorAll('.media-tab').forEach(tab => {
    const active = tab.getAttribute('data-tab') === tabName;
    tab.classList.toggle('active', active);
  });

  document.querySelectorAll('.media-tab-content').forEach(content => {
    content.classList.remove('active');
    content.style.display = 'none';
  });

  if (tabName === 'library') {
    const el = document.getElementById('mediaLibraryTab');
    if (el) { el.classList.add('active'); el.style.display = 'block'; }
    loadMediaLibrary();
  }
  if (tabName === 'upload') {
    const el = document.getElementById('mediaUploadTab');
    if (el) { el.classList.add('active'); el.style.display = 'block'; }
  }
}

function loadMediaLibrary() {
  const gridContainer = document.getElementById('mediaGridContainer');
  if (!gridContainer) return;
  gridContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;"><p>Loading media...</p></div>';

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const params = new URLSearchParams({ page: mediaModalState.currentPage, per_page: 20 });
  if (mediaModalState.currentSearch) params.append('search', mediaModalState.currentSearch);

  const adminUrl = normalizeAdminBase();
  fetch(adminUrl + 'api/media.php?' + params.toString(), { method: 'GET', headers: { 'X-CSRF-Token': csrfToken }, credentials: 'include' })
    .then(r => r.json())
    .then(data => {
      if (data.success && data.media) renderMediaGrid(data.media);
      else gridContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;"><p>No media files found.</p></div>';
    })
    .catch(() => {
      gridContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #d63638;"><p>Error loading media.</p></div>';
    });
}

function renderMediaGrid(mediaItems) {
  const gridContainer = document.getElementById('mediaGridContainer');
  if (!gridContainer) return;
  if (!mediaItems || mediaItems.length === 0) {
    gridContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;"><p>No media files found.</p></div>';
    return;
  }

  let html = '';
  mediaItems.forEach(media => {
    const mediaId = parseInt(media.id);
    const isSelected = mediaModalState.allowMultiple
      ? mediaModalState.selectedMediaMultiple.some(m => parseInt(m.id) === mediaId)
      : (mediaModalState.selectedMedia && parseInt(mediaModalState.selectedMedia.id) === mediaId);

    const borderColor = isSelected ? '#0073aa' : '#ddd';
    const borderWidth = isSelected ? '3px' : '2px';
    html += `
      <div class="media-grid-item" data-media-id="${media.id}"
           style="border:${borderWidth} solid ${borderColor}; border-radius:4px; overflow:hidden; cursor:pointer; background:white; position:relative;"
           onclick='selectMediaItem(${media.id}, ${JSON.stringify(media.file_path || '')}, ${JSON.stringify(media.url || '')})'>
        <div style="position: relative; padding-top: 100%; background: #f0f0f0;">
          <img src="${escapeHtml(media.url)}" alt="${escapeHtml(media.original_name || '')}" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;">
          ${isSelected ? '<div class="media-selection-checkmark" style="position:absolute;top:5px;right:5px;background:#0073aa;color:white;border-radius:50%;width:24px;height:24px;display:flex;align-items:center;justify-content:center;font-size:14px;z-index:10;">✓</div>' : ''}
        </div>
        <div style="padding: 8px; font-size: 12px; color: #666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${escapeHtml(media.original_name || '')}</div>
      </div>
    `;
  });
  gridContainer.innerHTML = html;
  updateInsertButton();
}

function selectMediaItem(mediaId, mediaPath, mediaUrl) {
  mediaId = parseInt(mediaId);
  if (mediaModalState.allowMultiple) {
    const idx = mediaModalState.selectedMediaMultiple.findIndex(m => parseInt(m.id) === mediaId);
    if (idx >= 0) mediaModalState.selectedMediaMultiple.splice(idx, 1);
    else mediaModalState.selectedMediaMultiple.push({ id: mediaId, path: mediaPath, url: mediaUrl });
    mediaModalState.selectedMedia = null;
  } else {
    mediaModalState.selectedMedia = { id: mediaId, path: mediaPath, url: mediaUrl };
    mediaModalState.selectedMediaMultiple = [];
  }
  loadMediaLibrary();
}

function updateInsertButton() {
  const insertBtn = document.getElementById('insertMediaBtn');
  const insertCount = document.getElementById('insertCount');
  const hasSelection = mediaModalState.allowMultiple ? mediaModalState.selectedMediaMultiple.length > 0 : !!mediaModalState.selectedMedia;
  const selectionCount = mediaModalState.allowMultiple ? mediaModalState.selectedMediaMultiple.length : (mediaModalState.selectedMedia ? 1 : 0);

  if (insertBtn) insertBtn.style.display = hasSelection ? 'inline-block' : 'none';
  if (insertCount) insertCount.textContent = String(selectionCount);
}

function insertSelectedMedia() {
  const selected = mediaModalState.allowMultiple ? mediaModalState.selectedMediaMultiple : (mediaModalState.selectedMedia ? [mediaModalState.selectedMedia] : []);
  if (selected.length === 0) return;

  if (window.insertSelectedMediaOverride && typeof window.insertSelectedMediaOverride === 'function') {
    const handled = window.insertSelectedMediaOverride();
    if (handled === true) return;
  }

  const first = selected[0];
  const input = document.getElementById(mediaModalState.targetInputId);
  if (input) input.value = mediaModalState.allowMultiple && selected.length > 1 ? JSON.stringify(selected.map(s => s.path)) : first.path;

  const preview = mediaModalState.targetPreviewId ? document.getElementById(mediaModalState.targetPreviewId) : null;
  if (preview) {
    preview.style.display = 'block';
    preview.innerHTML = '';
    const img = document.createElement('img');
    img.src = first.url;
    img.style.cssText = 'max-width: 100%; max-height: 300px; border-radius: 4px; display: block;';
    preview.appendChild(img);
  }

  closeMediaModal();
  if (typeof showToast === 'function') showToast('Image selected successfully', 'success');
}

function isAllowedImageFile(file) {
  if (!file || !file.name) return false;
  const type = (file.type || '').toLowerCase();
  const byMime = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(type);
  if (byMime) return true;
  return /\.(jpe?g|png|webp)$/i.test(file.name);
}

function handleMediaFileSelect(ev) {
  const input = ev && ev.target ? ev.target : document.getElementById('mediaFileInput');
  const info = document.getElementById('selectedFilesInfo');
  if (!input || !info) return;
  if (!input.files || input.files.length === 0) {
    info.style.display = 'none';
    info.textContent = '';
    return;
  }
  const names = Array.from(input.files).map(function(f) { return f.name; }).join(', ');
  info.textContent = input.files.length === 1 ? ('Selected: ' + names) : (String(input.files.length) + ' files: ' + names);
  info.style.display = 'block';
}

function uploadNewMedia() {
  const fileInput = document.getElementById('mediaFileInput');
  const submitBtn = document.getElementById('uploadSubmitBtn');
  if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
    if (typeof showToast === 'function') showToast('Choose one or more images first, then tap Upload.', 'error');
    return;
  }

  const files = Array.from(fileInput.files);
  const validFiles = files.filter(isAllowedImageFile);
  if (validFiles.length === 0) {
    if (typeof showToast === 'function') {
      showToast('Those files are not accepted. Use JPEG, PNG, or WebP (by extension or type).', 'error');
    }
    return;
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  if (!csrfToken) {
    if (typeof showToast === 'function') showToast('Session security token missing. Refresh the page and try again.', 'error');
    return;
  }

  const uploadUrl = normalizeAdminBase() + 'api/media.php';
  const originalText = submitBtn ? submitBtn.textContent : '';
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.textContent = 'Uploading ' + String(validFiles.length) + ' file(s)...';
  }

  var uploadedCount = 0;
  var failedCount = 0;

  function finishUploads() {
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
    if (typeof showToast === 'function') {
      if (uploadedCount > 0 && failedCount === 0) {
        showToast(String(uploadedCount) + ' file(s) uploaded.', 'success');
      } else if (uploadedCount > 0 && failedCount > 0) {
        showToast(String(uploadedCount) + ' uploaded, ' + String(failedCount) + ' failed.', 'warning');
      } else if (failedCount > 0) {
        showToast('Upload failed. Check the error messages above or try again.', 'error');
      }
    }
    fileInput.value = '';
    handleMediaFileSelect({ target: fileInput });
    switchMediaTab('library');
  }

  function uploadNext(index) {
    if (index >= validFiles.length) {
      finishUploads();
      return;
    }
    var file = validFiles[index];
    var formData = new FormData();
    formData.append('file', file);
    formData.append('csrf_token', csrfToken);

    fetch(uploadUrl, { method: 'POST', credentials: 'include', body: formData })
      .then(function(r) { return r.text().then(function(text) { return { r: r, text: text }; }); })
      .then(function(res) {
        var data;
        try {
          data = JSON.parse(res.text);
        } catch (err) {
          failedCount++;
          if (typeof showToast === 'function') {
            showToast('Server did not return JSON (HTTP ' + String(res.r.status) + '). Try logging in again.', 'error');
          }
          uploadNext(index + 1);
          return;
        }
        if (data.success) {
          uploadedCount++;
        } else {
          failedCount++;
          if (typeof showToast === 'function') {
            showToast(data.message || 'Upload rejected.', 'error');
          }
        }
        uploadNext(index + 1);
      })
      .catch(function() {
        failedCount++;
        if (typeof showToast === 'function') showToast('Network error while uploading.', 'error');
        uploadNext(index + 1);
      });
  }

  uploadNext(0);
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text || '';
  return div.innerHTML;
}

