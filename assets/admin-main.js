function untilDOMLoaded() {
  return new Promise(res => {
    document.addEventListener("DOMContentLoaded", res);
  });
}

const rangeInputId = "woocommerce_wc_consor_finanz_defaultduration";
const rangePreviewId = "woocommerce_wc_consor_finanz_defaultduration_preview";
let rangeInput;
let rangePreview;

function addRangePreviewBox() {
  document
    .getElementById(rangeInputId)
    .insertAdjacentHTML(
      "afterend",
      `<span id="${rangeInputId}_preview"></span>`
    );
  return document.getElementById(rangePreviewId);
}

function updateRangePreview(value) {
  document.getElementById(rangePreviewId).innerText = value;
}

function addRangeEventListener() {
  updateRangePreview(rangeInput.value);
  rangeInput.addEventListener("input", function() {
    updateRangePreview(this.value);
  });
}

function addMissingAttributesToRangeInput() {
  rangeInput.setAttribute("max", 72);
  rangeInput.setAttribute("min", 6);
  rangeInput.setAttribute("step", 2);
}

async function main() {
  await untilDOMLoaded();
  rangeInput = document.getElementById(rangeInputId);
  addMissingAttributesToRangeInput();
  rangePreview = addRangePreviewBox();
  addRangeEventListener();
}

main();
