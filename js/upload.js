// upload.js
const dropArea = document.getElementById("drop-area");
const dropAreaContent = document.getElementById("drop-area-content");
const fileInput = document.getElementById("image");
const btn_upload = document.getElementById("btn_upload");
const btn_cancel = document.getElementById("btn_cancel");
const block_message = document.getElementById("block_message");
const progressBar = document.getElementById("progressBar");
const progressWrapper = document.getElementById("progressWrapper");
const form = document.getElementById("uploadForm");
const tooltip = new bootstrap.Tooltip(progressWrapper, { html: true, animation: false });

const xhr = new XMLHttpRequest();
let setTimeoutId;
const initDropAreaText = dropAreaContent.innerText;
const MAX_FILE_SIZE = 1024; // 1 GB in KB

// FUNCTIONS

function preventDefaults(e) {
  e.preventDefault();
  e.stopPropagation();
}

function highlight() {
  dropArea.classList.add("highlight");
}

function unhighlight() {
  dropArea.classList.remove("highlight");
}

function handleDrop(e) {
  const dt = e.dataTransfer;
  const files = dt.files;

  // Create a new DataTransfer object
  const dataTransfer = new DataTransfer();

  // Add the files to the DataTransfer object
  for (let i = 0; i < files.length; i++) {
    dataTransfer.items.add(files[i]);
  }

  // Assign the new DataTransfer to fileInput.files
  fileInput.files = dataTransfer.files;

  handleFiles(fileInput.files); // Call the handler to process files
}

function dropAreaPrint(text) {
  dropAreaContent.textContent = text;
  dropAreaContent.title = text;
}

function messageAreaPrint(text, danger = false) {
  if (danger) {
    block_message.classList.remove("alert-success");
    block_message.classList.add("alert-danger");
  } else {
    block_message.classList.remove("alert-danger");
    block_message.classList.add("alert-success");
  }
  block_message.classList.remove("d-none");
  block_message.innerText = text;
  if (!danger) delayHideBlockMessage();
}

function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${minutes}m ${remainingSeconds}s`;
}

function setProgress(value, elapsedTime = 0, estimatedTimeRemaining = 0) {
  const perc_value = value + "%";
  if (progressBar.style.width == perc_value) {
    return;
  }
  progressBar.style.width = perc_value;
  // progressWrapper.title = "Progress: " + value + "%";
  const barValue = `Progress: ${value}%<br>
  Elapsed time: ${formatTime(elapsedTime)}<br>
  Estimated time remaining: ${formatTime(estimatedTimeRemaining)}`;
  // progressWrapper.title = barValue;
  // Manually update the tooltip
  tooltip.setContent({ ".tooltip-inner": barValue });
}

function hideProgress() {
  setTimeout(() => {
    progressWrapper.classList.add("d-none");
    setProgress(0);
    dropAreaPrint(initDropAreaText);
  }, 500);
}

function handleFiles(files) {
  if (xhr.readyState === XMLHttpRequest.OPENED) {
    console.error("Upload in progress: " + xhr.readyState);
    return; // Prevent dropping new files when uploading is in progress
  }
  let sizeInMb = 0;
  if (files.length > 0) {
    fileInput.files = files; // Assign dropped or selected files to input
    const fileSize = fileInput.files[0].size;
    sizeInMb = (fileSize / (1024 * 1024)).toFixed(2);
    dropAreaPrint('Selected file: "' + files[0].name + '". The file size is ' + sizeInMb + " MB"); // Display file name
  } else if (fileInput.files.length > 0) {
    const fileSize = fileInput.files[0].size;
    sizeInMb = (fileSize / (1024 * 1024)).toFixed(2);
    dropAreaPrint('Selected file: "' + fileInput.files[0].name + '"' + ". The file size is " + sizeInMb + " MB");
  }
  if (sizeInMb < MAX_FILE_SIZE) {
    btn_upload.classList.remove("disabled"); // Enable upload button
  } else {
    messageAreaPrint(`The file size must be less than ${MAX_FILE_SIZE} KB.`, true);
  }
}

function hideBlockMessage() {
  if (block_message) {
    if (setTimeoutId !== null) clearTimeout(setTimeoutId);
    block_message.classList.add("d-none");
  }
}

function delayHideBlockMessage() {
  if (block_message) {
    if (setTimeoutId === null) setTimeoutId = 0;
    clearTimeout(setTimeoutId);
    setTimeoutId = setTimeout(() => {
      hideBlockMessage();
    }, 60000);
  }
}

function upload_form(formData) {
  progressWrapper.classList.remove("d-none");
  btn_cancel.classList.remove("d-none");
  let uploadStartTime;

  // Start upload and track start time
  xhr.upload.addEventListener(
    "loadstart",
    () => {
      uploadStartTime = new Date().getTime();
    },
    false
  );

  // Update progress bar during the upload
  xhr.upload.addEventListener(
    "progress",
    function (e) {
      if (e.lengthComputable) {
        let currentTime = new Date().getTime();
        let elapsedTime = (currentTime - uploadStartTime) / 1000; // in seconds
        let estimatedTotalTime = (elapsedTime / e.loaded) * e.total;
        let estimatedTimeRemaining = Math.round(estimatedTotalTime - elapsedTime);
        let percentComplete = Math.round((e.loaded / e.total) * 100);
        setProgress(percentComplete, Math.round(elapsedTime), estimatedTimeRemaining);
      }
    },
    false
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      hideProgress();
      btn_cancel.classList.add("d-none");
      if (xhr.status === 200) {
        let response;
        try {
          response = JSON.parse(xhr.responseText);
          messageAreaPrint(response.message, response.error);
        } catch (err) {
          // response = xhr.responseText;
          console.error("Error parsing response:", err);
          messageAreaPrint("Upload failed. Check server logs for details.", true);
        }
      } else if (xhr.status === 0) {
        messageAreaPrint("Upload cancelled.", true);
      } else {
        messageAreaPrint("Upload failed. Status: " + xhr.status, true);
      }
    }
  };

  xhr.open(formData.method, formData.action, true);
  xhr.send(formData);
  hideBlockMessage();
}

btn_cancel.addEventListener("click", (e) => {
  e.preventDefault();
  if (xhr) {
    xhr.abort(); // Cancels the ongoing request
  }
});

// btn_upload
btn_upload.addEventListener("click", (e) => {
  e.preventDefault();

  btn_upload.classList.add("disabled"); // Enable upload button
  textContent = dropAreaContent.textContent.split(":").slice(-1)[0].trim();
  dropAreaPrint(`The file ${textContent} is starting the upload...`);
  // const newDropArea = dropArea.cloneNode(true); // Clones the element without event listeners
  // dropArea.parentNode.replaceChild(newDropArea, dropArea); // Replace the old element
  let formData = new FormData();
  formData.method = form.method;
  formData.action = form.action;
  formData.encType = form.enctype;
  const formElements = form.elements;
  Array.from(formElements).forEach((element) => {
    if (element.name && element.type !== "file") {
      formData.append(element.name, element.value); // Append name and value to FormData
    }
  });
  if (fileInput.files.length > 0) {
    formData.append("image", fileInput.files[0]);
  }

  upload_form(formData);
});
// btn_upload

// MAIN CODE

// LISTENERS

// Prevent default behaviors (prevent file from being opened)
["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
  dropArea.addEventListener(eventName, preventDefaults, false);
  document.body.addEventListener(eventName, preventDefaults, false);
});

// Highlight drop area when item is dragged over it
["dragenter", "dragover"].forEach((eventName) => {
  dropArea.addEventListener(eventName, highlight, false);
});

// Remove highlight when item is dragged out
["dragleave", "drop"].forEach((eventName) => {
  dropArea.addEventListener(eventName, unhighlight, false);
});

// Handle dropped files
dropArea.addEventListener("drop", handleDrop, false);

// Handle selected files
fileInput.addEventListener("change", handleFiles, false);

// Optional: Trigger click on drop area
dropArea.addEventListener("click", () => {
  if (xhr.readyState === XMLHttpRequest.OPENED) {
    console.error("Upload in progress: " + xhr.readyState);
    return; // Prevent dropping new files when uploading is in progress
  }
  fileInput.click();
});

delayHideBlockMessage();
