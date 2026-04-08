<?php
$pageTitle = 'About Page';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/header.php';

$sections = [];
try {
    $stmt = $pdo->prepare("SELECT section_key, content FROM page_sections WHERE page = 'about'");
    $stmt->execute();
    foreach ($stmt->fetchAll() as $row) {
        $sections[$row['section_key']] = $row['content'];
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}
$d = require __DIR__ . '/../../includes/cms-defaults.php';
$timelineDef = json_encode($d['about_timeline'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$teamDef = json_encode($d['about_team'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$timelineRaw = trim($sections['timeline_json'] ?? '') !== '' ? $sections['timeline_json'] : $timelineDef;
$teamRaw = trim($sections['team_json'] ?? '') !== '' ? $sections['team_json'] : $teamDef;
?>

<form id="aboutPageForm">
  <div class="card"><div class="card-header"><h2>Meta & hero</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="page_title">Page title</label>
      <input type="text" id="page_title" name="page_title" value="<?= sanitize($sections['page_title'] ?? 'The Lusso Legacy - About Us') ?>">
    </div>
    <div class="form-group">
      <label for="hero_established">Hero kicker</label>
      <input type="text" id="hero_established" name="hero_established" value="<?= sanitize($sections['hero_established'] ?? 'Established 2024') ?>">
    </div>
    <div class="form-group">
      <label for="hero_title_html">Hero title (HTML)</label>
      <textarea id="hero_title_html" name="hero_title_html" rows="3"><?= htmlspecialchars($sections['hero_title_html'] ?? 'The Lusso <br/><span class="font-bold italic text-primary/90 lusso-hero-accent-text">Legacy</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-group">
      <label for="hero_subtitle">Hero subtitle</label>
      <textarea id="hero_subtitle" name="hero_subtitle" rows="2"><?= sanitize($sections['hero_subtitle'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>Hero background</label>
      <button type="button" class="btn btn-outline" onclick="openMediaModal('hero_bg','hero_bg_preview')">Select</button>
      <input type="hidden" id="hero_bg" name="hero_bg" value="<?= sanitize($sections['hero_bg'] ?? '') ?>">
      <div id="hero_bg_preview" class="image-preview" style="<?= !empty($sections['hero_bg']) ? 'display:block;' : 'display:none;' ?>">
        <?php if (!empty($sections['hero_bg'])): ?>
          <img src="<?= SITE_URL . ltrim($sections['hero_bg'], '/') ?>" style="max-width:500px;">
        <?php endif; ?>
      </div>
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Story block</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="story_title_html">Title (HTML)</label>
      <textarea id="story_title_html" name="story_title_html" rows="2"><?= htmlspecialchars($sections['story_title_html'] ?? 'Defining Abuja <br/><span class="font-semibold text-primary">Luxury</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-group"><textarea name="story_p1" id="story_p1" rows="3"><?= sanitize($sections['story_p1'] ?? '') ?></textarea></div>
    <div class="form-group"><textarea name="story_p2" id="story_p2" rows="3"><?= sanitize($sections['story_p2'] ?? '') ?></textarea></div>
    <div class="form-group">
      <label>Story image</label>
      <button type="button" class="btn btn-outline" onclick="openMediaModal('story_image','story_image_preview')">Select</button>
      <input type="hidden" id="story_image" name="story_image" value="<?= sanitize($sections['story_image'] ?? '') ?>">
      <div id="story_image_preview" class="image-preview" style="<?= !empty($sections['story_image']) ? 'display:block;' : 'display:none;' ?>">
        <?php if (!empty($sections['story_image'])): ?>
          <img src="<?= SITE_URL . ltrim($sections['story_image'], '/') ?>" style="max-width:400px;">
        <?php endif; ?>
      </div>
    </div>
    <div class="form-group">
      <label for="story_quote">Pull quote</label>
      <textarea id="story_quote" name="story_quote" rows="2"><?= sanitize($sections['story_quote'] ?? '"A sanctuary where time slows down and memories are crafted."') ?></textarea>
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Values / artistry</h2></div><div style="padding:20px;">
    <div class="form-group"><label for="values_kicker">Kicker</label><input type="text" id="values_kicker" name="values_kicker" value="<?= sanitize($sections['values_kicker'] ?? 'Our Core Values') ?>"></div>
    <div class="form-group"><label for="values_title">Title</label><input type="text" id="values_title" name="values_title" value="<?= sanitize($sections['values_title'] ?? 'Artistry in Service') ?>"></div>
    <div class="form-group">
      <label>Large image</label>
      <button type="button" class="btn btn-outline" onclick="openMediaModal('values_image','values_image_preview')">Select</button>
      <input type="hidden" id="values_image" name="values_image" value="<?= sanitize($sections['values_image'] ?? '') ?>">
      <div id="values_image_preview" class="image-preview" style="<?= !empty($sections['values_image']) ? 'display:block;' : 'display:none;' ?>">
        <?php if (!empty($sections['values_image'])): ?>
          <img src="<?= SITE_URL . ltrim($sections['values_image'], '/') ?>" style="max-width:500px;">
        <?php endif; ?>
      </div>
    </div>
    <div class="form-group"><label for="values_card_icon">Card icon (Material)</label><input type="text" id="values_card_icon" name="values_card_icon" value="<?= sanitize($sections['values_card_icon'] ?? 'spa') ?>"></div>
    <div class="form-group"><label for="values_card_title">Card title</label><input type="text" id="values_card_title" name="values_card_title" value="<?= sanitize($sections['values_card_title'] ?? 'Sanctuary for the Senses') ?>"></div>
    <div class="form-group"><textarea name="values_card_body" id="values_card_body" rows="3"><?= sanitize($sections['values_card_body'] ?? '') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label for="values_card_link">Card link text</label><input type="text" id="values_card_link" name="values_card_link" value="<?= sanitize($sections['values_card_link'] ?? 'Read about our wellness') ?>"></div>
      <div class="form-group"><label for="values_card_link_href">Card URL</label><input type="text" id="values_card_link_href" name="values_card_link_href" value="<?= sanitize($sections['values_card_link_href'] ?? 'amenities.php') ?>"></div>
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Timeline & team</h2></div><div style="padding:20px;">
    <p class="form-help" style="margin-top:0;">Visual editor. Items are saved as JSON behind the scenes.</p>

    <!-- Hidden fields saved to page_sections -->
    <textarea id="timeline_json" name="timeline_json" style="display:none;"><?= htmlspecialchars($timelineRaw, ENT_QUOTES, 'UTF-8') ?></textarea>
    <textarea id="team_json" name="team_json" style="display:none;"><?= htmlspecialchars($teamRaw, ENT_QUOTES, 'UTF-8') ?></textarea>

    <div class="card card--nested">
      <div class="card-header"><h3>Timeline</h3></div>
      <div class="card-body card-body--stack">
        <p class="form-help">Each item: <code>year</code>, <code>kind</code> (<code>circle</code>|<code>dot</code>|<code>dot_primary</code>), <code>title</code>, <code>body</code>.</p>
        <div id="timelineEditor"></div>
        <button type="button" class="btn btn-outline btn-sm" id="timelineAddBtn">Add timeline item</button>

        <details style="margin-top:14px;">
          <summary style="cursor:pointer; color: var(--text-muted);">Advanced JSON (optional)</summary>
          <textarea id="timeline_json_advanced" rows="10" style="margin-top:10px;font-family:monospace;font-size:12px;"></textarea>
          <div style="margin-top:10px;">
            <button type="button" class="btn btn-outline btn-sm" id="timelineApplyJsonBtn">Apply JSON</button>
          </div>
        </details>
      </div>
    </div>

    <div class="card card--nested" style="margin-top: 16px;">
      <div class="card-header"><h3>Team</h3></div>
      <div class="card-body card-body--stack">
        <p class="form-help">Each member: <code>name</code>, <code>role</code>, <code>image</code> (URL or media path).</p>
        <div id="teamEditor"></div>
        <button type="button" class="btn btn-outline btn-sm" id="teamAddBtn">Add team member</button>

        <details style="margin-top:14px;">
          <summary style="cursor:pointer; color: var(--text-muted);">Advanced JSON (optional)</summary>
          <textarea id="team_json_advanced" rows="10" style="margin-top:10px;font-family:monospace;font-size:12px;"></textarea>
          <div style="margin-top:10px;">
            <button type="button" class="btn btn-outline btn-sm" id="teamApplyJsonBtn">Apply JSON</button>
          </div>
        </details>
      </div>
    </div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Journey header</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label for="journey_title_html">Title (HTML)</label>
      <textarea id="journey_title_html" name="journey_title_html" rows="2"><?= htmlspecialchars($sections['journey_title_html'] ?? 'A Historic <span class="font-bold italic text-primary">Journey</span>', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <div class="form-group"><label for="journey_subtitle">Subtitle</label><input type="text" id="journey_subtitle" name="journey_subtitle" value="<?= sanitize($sections['journey_subtitle'] ?? 'Tracing the milestones that shaped our vision of hospitality.') ?>"></div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Leadership intro</h2></div><div style="padding:20px;">
    <div class="form-group"><label for="team_kicker">Kicker</label><input type="text" id="team_kicker" name="team_kicker" value="<?= sanitize($sections['team_kicker'] ?? 'Leadership') ?>"></div>
    <div class="form-group"><label for="team_heading">Heading</label><input type="text" id="team_heading" name="team_heading" value="<?= sanitize($sections['team_heading'] ?? 'The Curators') ?>"></div>
    <div class="form-group"><textarea name="team_intro" id="team_intro" rows="2"><?= sanitize($sections['team_intro'] ?? 'Meet the visionaries dedicated to crafting your perfect stay.') ?></textarea></div>
  </div></div>

  <div class="card"><div class="card-header"><h2>Parallax & CTA</h2></div><div style="padding:20px;">
    <div class="form-group">
      <label>Parallax image</label>
      <button type="button" class="btn btn-outline" onclick="openMediaModal('parallax_bg','parallax_bg_preview')">Select</button>
      <input type="hidden" id="parallax_bg" name="parallax_bg" value="<?= sanitize($sections['parallax_bg'] ?? '') ?>">
      <div id="parallax_bg_preview" class="image-preview" style="<?= !empty($sections['parallax_bg']) ? 'display:block;' : 'display:none;' ?>">
        <?php if (!empty($sections['parallax_bg'])): ?><img src="<?= SITE_URL . ltrim($sections['parallax_bg'], '/') ?>" style="max-width:500px;"><?php endif; ?>
      </div>
    </div>
    <div class="form-group"><label for="parallax_quote">Parallax quote</label><input type="text" id="parallax_quote" name="parallax_quote" value="<?= sanitize($sections['parallax_quote'] ?? '"Where elegance meets heritage."') ?>"></div>
    <div class="form-group"><label for="cta_title">CTA title</label><input type="text" id="cta_title" name="cta_title" value="<?= sanitize($sections['cta_title'] ?? 'Ready to Experience the Legend?') ?>"></div>
    <div class="form-group"><textarea name="cta_body" id="cta_body" rows="2"><?= sanitize($sections['cta_body'] ?? 'Your journey into the extraordinary begins here. Reserve your sanctuary in Abuja today.') ?></textarea></div>
    <div class="form-row">
      <div class="form-group"><label for="cta_btn1">Button 1</label><input type="text" id="cta_btn1" name="cta_btn1" value="<?= sanitize($sections['cta_btn1'] ?? 'Check Availability') ?>"></div>
      <div class="form-group"><label for="cta_btn1_href">URL</label><input type="text" id="cta_btn1_href" name="cta_btn1_href" value="<?= sanitize($sections['cta_btn1_href'] ?? 'rooms.php') ?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label for="cta_btn2">Button 2</label><input type="text" id="cta_btn2" name="cta_btn2" value="<?= sanitize($sections['cta_btn2'] ?? 'Contact Concierge') ?>"></div>
      <div class="form-group"><label for="cta_btn2_href">URL</label><input type="text" id="cta_btn2_href" name="cta_btn2_href" value="<?= sanitize($sections['cta_btn2_href'] ?? 'contact.php') ?>"></div>
    </div>
  </div></div>

  <button type="submit" class="btn btn-primary">Save all</button>
</form>

<script>
(function () {
  var map = { hero_bg: 'hero_bg_preview', story_image: 'story_image_preview', values_image: 'values_image_preview', parallax_bg: 'parallax_bg_preview' };
  window.insertSelectedMediaOverride = function () {
    var s = mediaModalState.selectedMedia;
    if (!s) return false;
    var pid = map[mediaModalState.targetInputId];
    if (!pid) return false;
    document.getElementById(mediaModalState.targetInputId).value = s.path;
    var p = document.getElementById(pid);
    p.style.display = 'block';
    p.innerHTML = '<img src="<?= SITE_URL ?>' + s.path.replace(/^\/+/, '') + '" style="max-width:500px;">';
    closeMediaModal();
    return true;
  };
})();

(function () {
  function safeParseJson(text, fallback) {
    try {
      var v = JSON.parse(text || '');
      return v;
    } catch (e) {
      return fallback;
    }
  }

  function escHtml(s) {
    return String(s || '').replace(/[&<>"']/g, function (m) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
    });
  }

  function normalizeTeamImageUrl(val) {
    var v = String(val || '').trim();
    if (!v) return '';
    if (v.indexOf('http') === 0) return v;
    return '<?= SITE_URL ?>' + v.replace(/^\/+/, '');
  }

  function getTimelineItemsFromDom() {
    var out = [];
    document.querySelectorAll('#timelineEditor .js-tl-item').forEach(function (row) {
      var year = (row.querySelector('.js-tl-year')?.value || '').trim();
      var kind = (row.querySelector('.js-tl-kind')?.value || 'dot').trim() || 'dot';
      var title = (row.querySelector('.js-tl-title')?.value || '').trim();
      var body = (row.querySelector('.js-tl-body')?.value || '').trim();
      if (!year && !title && !body) return;
      out.push({ year: year, kind: kind, title: title, body: body });
    });
    return out;
  }

  function getTeamItemsFromDom() {
    var out = [];
    document.querySelectorAll('#teamEditor .js-team-item').forEach(function (row) {
      var name = (row.querySelector('.js-team-name')?.value || '').trim();
      var role = (row.querySelector('.js-team-role')?.value || '').trim();
      var image = (row.querySelector('.js-team-image')?.value || '').trim();
      if (!name && !role && !image) return;
      out.push({ name: name, role: role, image: image });
    });
    return out;
  }

  function syncHiddenJson() {
    var tl = getTimelineItemsFromDom();
    var tm = getTeamItemsFromDom();
    var tlEl = document.getElementById('timeline_json');
    var tmEl = document.getElementById('team_json');
    if (tlEl) tlEl.value = JSON.stringify(tl);
    if (tmEl) tmEl.value = JSON.stringify(tm);
    var tlAdv = document.getElementById('timeline_json_advanced');
    var tmAdv = document.getElementById('team_json_advanced');
    if (tlAdv) tlAdv.value = JSON.stringify(tl, null, 2);
    if (tmAdv) tmAdv.value = JSON.stringify(tm, null, 2);
  }

  function renderTimeline(items) {
    var host = document.getElementById('timelineEditor');
    if (!host) return;
    host.innerHTML = '';
    (items || []).forEach(function (it, idx) {
      var year = (it && it.year) || '';
      var kind = (it && it.kind) || 'dot';
      var title = (it && it.title) || '';
      var body = (it && it.body) || '';
      var wrap = document.createElement('div');
      wrap.className = 'card js-tl-item';
      wrap.style.cssText = 'margin-bottom: 12px; padding: 12px;';
      wrap.innerHTML =
        '<div class="form-row" style="align-items:flex-end; gap: 10px;">' +
          '<div class="form-group" style="flex:0 0 120px; margin-bottom:0;">' +
            '<label>Year</label>' +
            '<input type="text" class="form-control js-tl-year" value="' + escHtml(year) + '" placeholder="2024">' +
          '</div>' +
          '<div class="form-group" style="flex:0 0 170px; margin-bottom:0;">' +
            '<label>Kind</label>' +
            '<select class="form-control js-tl-kind">' +
              '<option value="dot"' + (kind === 'dot' ? ' selected' : '') + '>dot</option>' +
              '<option value="dot_primary"' + (kind === 'dot_primary' ? ' selected' : '') + '>dot_primary</option>' +
              '<option value="circle"' + (kind === 'circle' ? ' selected' : '') + '>circle</option>' +
            '</select>' +
          '</div>' +
          '<div class="form-group" style="flex:1; margin-bottom:0;">' +
            '<label>Title</label>' +
            '<input type="text" class="form-control js-tl-title" value="' + escHtml(title) + '" placeholder="Milestone title">' +
          '</div>' +
          '<button type="button" class="btn btn-outline btn-sm js-tl-remove">Remove</button>' +
        '</div>' +
        '<div class="form-group" style="margin-bottom:0;">' +
          '<label>Body</label>' +
          '<textarea class="form-control js-tl-body" rows="3" placeholder="Short description">' + escHtml(body) + '</textarea>' +
        '</div>';
      wrap.querySelector('.js-tl-remove').addEventListener('click', function () {
        wrap.remove();
        syncHiddenJson();
      });
      wrap.addEventListener('input', function () { syncHiddenJson(); });
      wrap.addEventListener('change', function () { syncHiddenJson(); });
      host.appendChild(wrap);
    });
  }

  function renderTeam(items) {
    var host = document.getElementById('teamEditor');
    if (!host) return;
    host.innerHTML = '';
    (items || []).forEach(function (it, idx) {
      var name = (it && it.name) || '';
      var role = (it && it.role) || '';
      var image = (it && it.image) || '';
      var inputId = 'team_image_' + idx;
      var prevId = 'team_image_' + idx + '_preview';
      var imgUrl = normalizeTeamImageUrl(image);

      var wrap = document.createElement('div');
      wrap.className = 'card js-team-item';
      wrap.style.cssText = 'margin-bottom: 12px; padding: 12px;';
      wrap.innerHTML =
        '<div class="form-row" style="align-items:flex-end; gap: 10px;">' +
          '<div class="form-group" style="flex: 1; margin-bottom:0;">' +
            '<label>Name</label>' +
            '<input type="text" class="form-control js-team-name" value="' + escHtml(name) + '" placeholder="Full name">' +
          '</div>' +
          '<div class="form-group" style="flex: 1; margin-bottom:0;">' +
            '<label>Role</label>' +
            '<input type="text" class="form-control js-team-role" value="' + escHtml(role) + '" placeholder="Title / position">' +
          '</div>' +
          '<button type="button" class="btn btn-outline btn-sm js-team-remove">Remove</button>' +
        '</div>' +
        '<div class="form-group" style="margin-top:10px;">' +
          '<label>Image</label>' +
          '<div style="display:flex; gap: 10px; align-items:center; flex-wrap:wrap;">' +
            '<button type="button" class="btn btn-outline js-team-pick">Select from media</button>' +
            '<input type="text" id="' + escHtml(inputId) + '" class="form-control js-team-image" value="' + escHtml(image) + '" placeholder="/assets/uploads/… or https://…">' +
          '</div>' +
          '<div id="' + escHtml(prevId) + '" class="image-preview" style="' + (imgUrl ? 'display:block;margin-top:10px;' : 'display:none;margin-top:10px;') + '">' +
            (imgUrl ? ('<img src="' + escHtml(imgUrl) + '" style="max-width:260px;max-height:180px;border-radius:6px;">') : '') +
          '</div>' +
        '</div>';

      wrap.querySelector('.js-team-pick').addEventListener('click', function () {
        openMediaModal(inputId, prevId, false);
      });
      wrap.querySelector('.js-team-remove').addEventListener('click', function () {
        wrap.remove();
        syncHiddenJson();
      });
      wrap.addEventListener('input', function () {
        // Update preview if someone pastes a URL/path.
        var v = (wrap.querySelector('.js-team-image')?.value || '').trim();
        var p = document.getElementById(prevId);
        if (p) {
          var u = normalizeTeamImageUrl(v);
          if (!u) {
            p.style.display = 'none';
            p.innerHTML = '';
          } else {
            p.style.display = 'block';
            p.innerHTML = '<img src="' + escHtml(u) + '" style="max-width:260px;max-height:180px;border-radius:6px;">';
          }
        }
        syncHiddenJson();
      });
      wrap.addEventListener('change', function () { syncHiddenJson(); });
      host.appendChild(wrap);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var tlRaw = document.getElementById('timeline_json')?.value || '[]';
    var tmRaw = document.getElementById('team_json')?.value || '[]';
    var tl = safeParseJson(tlRaw, []);
    var tm = safeParseJson(tmRaw, []);
    if (!Array.isArray(tl)) tl = [];
    if (!Array.isArray(tm)) tm = [];

    // If empty, start with one blank item so the UI doesn't look broken.
    if (tl.length === 0) tl = [{ year: '', kind: 'dot', title: '', body: '' }];
    if (tm.length === 0) tm = [{ name: '', role: '', image: '' }];

    renderTimeline(tl);
    renderTeam(tm);
    syncHiddenJson();

    var tlAdd = document.getElementById('timelineAddBtn');
    if (tlAdd) tlAdd.addEventListener('click', function () {
      var cur = getTimelineItemsFromDom();
      cur.push({ year: '', kind: 'dot', title: '', body: '' });
      renderTimeline(cur);
      syncHiddenJson();
    });

    var tmAdd = document.getElementById('teamAddBtn');
    if (tmAdd) tmAdd.addEventListener('click', function () {
      var cur = getTeamItemsFromDom();
      cur.push({ name: '', role: '', image: '' });
      renderTeam(cur);
      syncHiddenJson();
    });

    var tlApply = document.getElementById('timelineApplyJsonBtn');
    if (tlApply) tlApply.addEventListener('click', function () {
      var t = document.getElementById('timeline_json_advanced')?.value || '';
      var v = safeParseJson(t, null);
      if (!Array.isArray(v)) {
        showToast('Timeline JSON must be an array', 'error');
        return;
      }
      renderTimeline(v);
      syncHiddenJson();
      showToast('Timeline applied', 'success');
    });

    var tmApply = document.getElementById('teamApplyJsonBtn');
    if (tmApply) tmApply.addEventListener('click', function () {
      var t = document.getElementById('team_json_advanced')?.value || '';
      var v = safeParseJson(t, null);
      if (!Array.isArray(v)) {
        showToast('Team JSON must be an array', 'error');
        return;
      }
      renderTeam(v);
      syncHiddenJson();
      showToast('Team applied', 'success');
    });
  });
})();
document.getElementById('aboutPageForm').addEventListener('submit', function (e) {
  e.preventDefault();
  savePageForm(this, 'about')
    .then(function () { showToast('Saved', 'success'); })
    .catch(function (err) { showToast(err.message || 'Save failed', 'error'); });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
