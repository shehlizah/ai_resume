# Video Demo Guide - YouTube Integration

## Overview
The homepage uses **YouTube embeds** for 30-second demo videos. Videos appear in a modal when users click "▶ Watch 30s demo" on the feature cards.

## Quick Setup

### Step 1: Upload Videos to YouTube
1. Create videos (20-40 seconds each) - see specs below
2. Upload to your YouTube channel
3. Set videos as **Unlisted** (not Private, not Public)
4. Copy the video IDs from YouTube URLs

### Step 2: Update Video IDs in Code

Open: `resources/views/frontend/pages/home.blade.php`

Find the `videoData` object and replace the placeholder IDs:

```javascript
const videoData = {
    'cv-demo': {
        youtubeId: 'YOUR_VIDEO_ID_HERE',  // Replace this
        caption: 'Create professional CVs in minutes with our easy builder'
    },
    'jobs-demo': {
        youtubeId: 'YOUR_VIDEO_ID_HERE',  // Replace this
        caption: 'Find jobs that match your skills and preferences'
    },
    'interview-demo': {
        youtubeId: 'YOUR_VIDEO_ID_HERE',  // Replace this
        caption: 'Practice interviews with AI-powered feedback'
    },
    'hired-demo': {
        youtubeId: 'YOUR_VIDEO_ID_HERE',  // Replace this
        caption: 'Track your progress and land your dream job'
    }
};
```

**How to get YouTube Video ID:**
- YouTube URL: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`
- Video ID: `dQw4w9WgXcQ` (the part after `v=`)

---

## Video Requirements

### General Specifications
- **Duration**: 20-40 seconds (30 seconds ideal)
- **Platform**: YouTube (upload as Unlisted)
- **Resolution**: 1920x1080 (Full HD) recommended
- **Aspect Ratio**: 16:9
- **Quality**: HD or better for crisp playback

### Why YouTube?
✅ **Better**: No bandwidth costs, fast CDN delivery
✅ **Better**: Works on all devices automatically
✅ **Better**: YouTube handles compression and formats
✅ **Better**: Easy to update - just change video ID
✅ **Better**: Analytics built-in (views, watch time)

### Content Style
- ✅ **DO**: Simple screen recordings with on-screen captions
- ✅ **DO**: Show the actual interface in action
- ✅ **DO**: Focus on one clear message per video
- ✅ **DO**: Use text overlays to highlight key features
- ❌ **DON'T**: Include talking heads or voice-over (optional)
- ❌ **DON'T**: Make videos longer than 40 seconds
- ❌ **DON'T**: Show too many features at once

### Mobile Behavior
- **Desktop**: Videos auto-play when modal opens
- **Mobile**: Videos require explicit tap (prevents bounce)
- YouTube player includes standard controls automatically
- Responsive iframe adapts to all screen sizes

---

## Required Videos

### 1. CV Builder Demo
**YouTube Video ID needed**: Replace `'cv-demo'` in code

**Duration**: 20-30 seconds

**Message**: "Create professional CVs in minutes"

**What to Show** (30 seconds):
1. (0-8s) Starting screen → Click "Create CV"
2. (8-16s) Select template → Preview
3. (16-24s) Quick form fill (time-lapse style)
4. (24-30s) Final CV → Download button

**Caption**: "Create professional CVs in minutes with our easy builder"

---

### 2. Job Finder Demo
**YouTube Video ID needed**: Replace `'jobs-demo'` in code

**Duration**: 20-30 seconds

**Message**: "Find jobs matching your profile instantly"

**What to Show** (30 seconds):
1. (0-8s) Jobs page loading → List appears
2. (8-16s) Use filters (location, skills)
3. (16-24s) Click job → View details
4. (24-30s) Save/Apply button

**Caption**: "Find jobs that match your skills and preferences"

---

### 3. Interview Practice Demo
**YouTube Video ID needed**: Replace `'interview-demo'` in code

**Duration**: 20-30 seconds

**Message**: "Practice with AI, get instant feedback"

**What to Show** (30 seconds):
1. (0-8s) Interview prep page → Select category
2. (8-16s) Question appears → User types answer
3. (16-24s) AI feedback appears with score
4. (24-30s) Next question or review screen

**Caption**: "Practice interviews with AI-powered feedback"

---

### 4. Get Hired/Dashboard Demo
**YouTube Video ID needed**: Replace `'hired-demo'` in code

**Duration**: 20-30 seconds

**Message**: "Track progress, manage applications"

**What to Show** (30 seconds):
1. (0-8s) Dashboard overview → Stats cards
2. (8-16s) Recent applications list
3. (16-24s) Quick action buttons (Create CV, Apply Job)
4. (24-30s) Upgrade prompt or success metrics

**Caption**: "Track your progress and land your dream job"

---

## YouTube Upload Settings

### Recommended Settings
1. **Visibility**: **Unlisted** (not Private)
   - Unlisted = Anyone with link can watch
   - Private = Won't work in embeds
   - Public = Will show on your channel

2. **Title**: Keep it descriptive
   - "Jobsease - CV Builder Demo"
   - "Jobsease - Job Search Demo"
   - etc.

3. **Description**: Add context
   ```
   Quick 30-second demo showing how to create professional CVs 
   using Jobsease's easy builder tool.
   
   Visit: https://jobsease.com
   ```

4. **Thumbnail**: Create custom thumbnail
   - 1280x720 pixels
   - Clear text overlay
   - Shows main UI element

5. **End Screen**: Disable (videos are short)

6. **Cards**: Optional - add link to your site

---

## Video Production

### Tools to Use
- **Screen Recording**: OBS Studio, Loom, or QuickTime (Mac)
- **Editing**: DaVinci Resolve (free), iMovie, or Kapwing
- **Captions**: YouTube auto-generates or add manually
- **Upload**: Directly to YouTube Studio

### Recording Checklist
1. Clear browser cache and use clean test account
2. Hide personal information (names, emails)
3. Use smooth mouse movements
4. Record at 60fps for smoothness
5. Upload directly to YouTube (no compression needed)
6. Set as Unlisted
7. Copy video ID and update code
8. Test modal on live site

### Caption Tips (YouTube Auto-Captions)
YouTube will auto-generate captions, or add your own:
- Use the YouTube Studio editor
- Add key text overlays in video editing before upload
- Keep text large and readable (40-60px in video)

---

## Implementation Steps

### 1. Create Your Videos
Record 4 short demos (20-30 seconds each) following the content guide above.

### 2. Upload to YouTube
- Go to YouTube Studio
- Upload each video
- Set visibility to **Unlisted**
- Add titles and descriptions
- Wait for processing to complete

### 3. Get Video IDs
From each video's URL:
```
https://www.youtube.com/watch?v=dQw4w9WgXcQ
                                ^^^^^^^^^^^
                                Video ID
```

### 4. Update Code
Edit: `resources/views/frontend/pages/home.blade.php`

Find around line ~1970 and replace the placeholder IDs:
```javascript
const videoData = {
    'cv-demo': {
        youtubeId: 'ABC123XYZ',  // Your actual video ID
        caption: '...'
    },
    // ... repeat for all 4 videos
};
```

### 5. Test
- Open homepage
- Hover over feature cards
- Click "▶ Watch 30s demo"
- Verify video plays correctly
- Test on mobile (should not auto-play)

---

## Testing Checklist

- [ ] Videos are Unlisted (not Private)
- [ ] All 4 video IDs updated in code
- [ ] Modal opens smoothly on desktop
- [ ] Videos auto-play on desktop
- [ ] Videos don't auto-play on mobile
- [ ] Close button works (X and Escape key)
- [ ] Videos stop when modal closes
- [ ] Responsive on all screen sizes
- [ ] YouTube player controls visible

---

## Example Configuration

```javascript
// Real example after you upload your videos
const videoData = {
    'cv-demo': {
        youtubeId: 'dQw4w9WgXcQ',
        caption: 'Create professional CVs in minutes with our easy builder'
    },
    'jobs-demo': {
        youtubeId: 'J---aiyznGQ',
        caption: 'Find jobs that match your skills and preferences'
    },
    'interview-demo': {
        youtubeId: 'kJQP7kiw5Fk',
        caption: 'Practice interviews with AI-powered feedback'
    },
    'hired-demo': {
        youtubeId: '9bZkp7q19f0',
        caption: 'Track your progress and land your dream job'
    }
};
```

---

## Temporary Placeholder

The code currently uses `dQw4w9WgXcQ` as a placeholder for all videos. This is a valid YouTube video ID that will work for testing, but replace it with your actual demo videos.

---

## Benefits of YouTube Over Direct Upload

✅ **No hosting costs** - YouTube handles storage and bandwidth
✅ **Global CDN** - Fast loading worldwide
✅ **Auto-quality** - Adjusts to user's connection speed
✅ **Mobile optimized** - Works perfectly on all devices
✅ **Easy updates** - Just change the video ID
✅ **Analytics** - See views and engagement in YouTube Studio
✅ **Subtitles** - Auto-generated captions available

---

## Troubleshooting

**Video not loading?**
- Check if video is Unlisted (not Private)
- Verify video ID is correct
- Check browser console for errors

**Auto-play not working?**
- Some browsers block auto-play
- YouTube iframe API handles this automatically
- Mobile correctly requires user tap

**Modal not closing?**
- Clear browser cache
- Check JavaScript console for errors
- Verify closeVideoModal() function exists

---

## Future Enhancements

Consider adding:
- Video thumbnail preview on card
- Play count tracking
- Multiple videos per feature
- Playlist support
- Video progress indicator
