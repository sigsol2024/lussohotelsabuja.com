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
  currentSearch: '',
  totalPages: 1,
  totalItems: 0,
  isLoading: false,
  /** Current library page items (for shift-range selection). */
  currentGridItems: [],
  /** Anchor row index for Shift+click range (grid index on current page). */
  shiftAnchorIndex: null
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

  const loadMoreBtn = document.getElementById('mediaLoadMoreBtn');
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function () {
      if (mediaModalState.isLoading) return;
      if (mediaModalState.currentPage >= mediaModalState.totalPages) return;
      mediaModalState.currentPage += 1;
      loadMediaLibrary({ append: true });
    });
  }

  const gridContainer = document.getElementById('mediaGridContainer');
  if (gridContainer) {
    gridContainer.addEventListener('click', function (e) {
      const cell = e.target.closest('.media-grid-item');
      if (!cell) return;
      e.preventDefault();
      const idx = parseInt(cell.getAttribute('data-media-idx'), 10);
      const media = mediaModalState.currentGridItems && mediaModalState.currentGridItems[idx];
      if (!media) return;
      selectMediaItem(
        parseInt(media.id, 10),
        media.file_path || '',
        media.url || '',
        idx,
        e
      );
    });
  }
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
  if (!targetInputId) return false;
  const multipleFields = [
    'gallery_image_new',
    'room_gallery_images',
    'gallery_images',
    'hero_bg_slides_pick'
  ];
  return multipleFields.some(field => targetInputId === field || targetInputId.includes(field));
}

function openMediaModal(targetInputId, targetPreviewId, allowMultiple = null) {
  mediaModalState.targetInputId = targetInputId;
  mediaModalState.targetPreviewId = targetPreviewId;
  mediaModalState.selectedMedia = null;
  mediaModalState.selectedMediaMultiple = [];
  mediaModalState.currentGridItems = [];
  mediaModalState.shiftAnchorIndex = null;
  mediaModalState.allowMultiple = allowMultiple === null ? shouldAllowMultipleSelection(targetInputId) : allowMultiple;
  mediaModalState.currentPage = 1;
  mediaModalState.totalPages = 1;
  mediaModalState.totalItems = 0;
  mediaModalState.isLoading = false;

  const modal = document.getElementById('mediaLibraryModal');
  if (!modal) return;
  const hint = document.getElementById('mediaMultiSelectHint');
  if (hint) hint.style.display = mediaModalState.allowMultiple ? 'block' : 'none';
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  switchMediaTab('library');
  loadMediaLibrary({ append: false });
  updateInsertButton();
}

function closeMediaModal() {
  const modal = document.getElementById('mediaLibraryModal');
  if (!modal) return;
  modal.style.display = 'none';
  document.body.style.overflow = '';
  mediaModalState.selectedMedia = null;
  mediaModalState.selectedMediaMultiple = [];
  mediaModalState.currentGridItems = [];
  mediaModalState.shiftAnchorIndex = null;
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
    loadMediaLibrary({ append: false });
  }
  if (tabName === 'upload') {
    const el = document.getElementById('mediaUploadTab');
    if (el) { el.classList.add('active'); el.style.display = 'block'; }
  }
}

function setLoadMoreUi() {
  const wrap = document.getElementById('mediaLoadMoreWrap');
  const btn = document.getElementById('mediaLoadMoreBtn');
  const status = document.getElementById('mediaLoadMoreStatus');
  if (!wrap || !btn || !status) return;

  const hasMore = mediaModalState.currentPage < mediaModalState.totalPages;
  wrap.style.display = (mediaModalState.totalItems > 0) ? 'block' : 'none';
  btn.style.display = hasMore ? 'inline-block' : 'none';
  btn.disabled = !!mediaModalState.isLoading;
  btn.textContent = mediaModalState.isLoading ? 'Loading…' : 'Load more';
  status.textContent = mediaModalState.totalItems
    ? ('Showing ' + String(mediaModalState.currentGridItems.length) + ' of ' + String(mediaModalState.totalItems))
    : '';
}

function loadMediaLibrary(opts) {
  opts = opts || {};
  const append = !!opts.append;
  const gridContainer = document.getElementById('mediaGridContainer');
  if (!gridContainer) return;

  if (!append) {
    mediaModalState.currentPage = 1;
    mediaModalState.currentGridItems = [];
    mediaModalState.shiftAnchorIndex = null;
    gridContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;"><p>Loading media...</p></div>';
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const params = new URLSearchParams({ page: mediaModalState.currentPage, per_page: 20 });
  if (mediaModalState.currentSearch) params.append('search', mediaModalState.currentSearch);

  const adminUrl = normalizeAdminBase();
  mediaModalState.isLoading = true;
  setLoadMoreUi();
  fetch(adminUrl + 'api/media.php?' + params.toString(), { method: 'GET', headers: { 'X-CSRF-Token': csrfToken }, credentials: 'include' })
    .then(r => r.json())
    .then(data => {
      mediaModalState.isLoading = false;
      if (data && data.pagination) {
        mediaModalState.totalPages = parseInt(data.pagination.pages || 1, 10) || 1;
        mediaModalState.totalItems = parseInt(data.pagination.total || 0, 10) || 0;
      } else {
        mediaModalState.totalPages = 1;
        mediaModalState.totalItems = 0;
      }

      if (data.success && Array.isArray(data.media)) {
        const merged = append ? (mediaModalState.currentGridItems.concat(data.media)) : data.media;
        renderMediaGrid(merged);
      } else if (!append) {
        gridContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;"><p>No media files found.</p></div>';
        mediaModalState.currentGridItems = [];
        setLoadMoreUi();
      } else {
        setLoadMoreUi();
      }
    })
    .catch(() => {
      mediaModalState.isLoading = false;
      if (!append) {
        gridContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #d63638;"><p>Error loading media.</p></div>';
        mediaModalState.currentGridItems = [];
      }
      setLoadMoreUi();
    });
}

function renderMediaGrid(mediaItems) {
  const gridContainer = document.getElementById('mediaGridContainer');
  if (!gridContainer) return;
  mediaModalState.currentGridItems = Array.isArray(mediaItems) ? mediaItems : [];
  if (!mediaItems || mediaItems.length === 0) {
    gridContainer.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;"><p>No media files found.</p></div>';
    setLoadMoreUi();
    return;
  }

  let html = '';
  mediaItems.forEach((media, gridIdx) => {
    const mediaId = parseInt(media.id, 10);
    const isSelected = mediaModalState.allowMultiple
      ? mediaModalState.selectedMediaMultiple.some(m => parseInt(m.id, 10) === mediaId)
      : (mediaModalState.selectedMedia && parseInt(mediaModalState.selectedMedia.id, 10) === mediaId);

    const borderColor = isSelected ? '#0073aa' : '#ddd';
    const borderWidth = isSelected ? '3px' : '2px';
    html += `
      <div class="media-grid-item" data-media-id="${media.id}" data-media-idx="${gridIdx}"
           style="border:${borderWidth} solid ${borderColor}; border-radius:4px; overflow:hidden; cursor:pointer; background:white; position:relative;">
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
  setLoadMoreUi();
}

/**
 * @param {number} gridIndex - Index in currentGridItems (current page), for shift-range selection.
 * @param {MouseEvent} [ev]
 */
function selectMediaItem(mediaId, mediaPath, mediaUrl, gridIndex, ev) {
  mediaId = parseInt(mediaId, 10);
  const item = { id: mediaId, path: mediaPath, url: mediaUrl };

  if (!mediaModalState.allowMultiple) {
    mediaModalState.selectedMedia = item;
    mediaModalState.selectedMediaMultiple = [];
    renderMediaGrid(mediaModalState.currentGridItems);
    updateInsertButton();
    return;
  }

  const items = mediaModalState.currentGridItems || [];
  const ctrl = ev && (ev.ctrlKey || ev.metaKey);
  const shift = ev && ev.shiftKey;

  if (shift && mediaModalState.shiftAnchorIndex != null && typeof gridIndex === 'number' && !isNaN(gridIndex)) {
    const a = Math.min(mediaModalState.shiftAnchorIndex, gridIndex);
    const b = Math.max(mediaModalState.shiftAnchorIndex, gridIndex);
    const next = [];
    const seen = new Set();
    for (let i = a; i <= b; i++) {
      const m = items[i];
      if (!m) continue;
      const id = parseInt(m.id, 10);
      if (seen.has(id)) continue;
      seen.add(id);
      next.push({ id, path: m.file_path || '', url: m.url || '' });
    }
    mediaModalState.selectedMediaMultiple = next;
    mediaModalState.selectedMedia = null;
    mediaModalState.shiftAnchorIndex = gridIndex;
  } else if (shift && typeof gridIndex === 'number' && !isNaN(gridIndex)) {
    mediaModalState.selectedMediaMultiple = [item];
    mediaModalState.selectedMedia = null;
    mediaModalState.shiftAnchorIndex = gridIndex;
  } else if (ctrl) {
    const list = mediaModalState.selectedMediaMultiple;
    const idx = list.findIndex(m => parseInt(m.id, 10) === mediaId);
    if (idx >= 0) list.splice(idx, 1);
    else list.push(item);
    mediaModalState.selectedMedia = null;
    if (typeof gridIndex === 'number' && !isNaN(gridIndex)) {
      mediaModalState.shiftAnchorIndex = gridIndex;
    }
  } else {
    mediaModalState.selectedMediaMultiple = [item];
    mediaModalState.selectedMedia = null;
    if (typeof gridIndex === 'number' && !isNaN(gridIndex)) {
      mediaModalState.shiftAnchorIndex = gridIndex;
    }
  }

  renderMediaGrid(mediaModalState.currentGridItems);
  updateInsertButton();
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
  if (input) {
    input.value = mediaModalState.allowMultiple && selected.length > 1
      ? JSON.stringify(selected.map(s => s.path))
      : first.path;
    // IMPORTANT: many page editors sync hidden JSON on input/change events.
    // When we set values programmatically, dispatch events so those editors persist selections.
    try {
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    } catch (e) {}
  }

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
  if (typeof showToast === 'function') {
    const n = selected.length;
    showToast(n > 1 ? (n + ' images selected') : 'Image selected successfully', 'success');
  }
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

