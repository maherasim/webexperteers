(function () {
  "use strict";

  // Live preview for Bootstrap Icon inputs
  document.querySelectorAll(".icon-input").forEach((input) => {
    const icon = input.closest(".input-group").querySelector(".icon-preview i");
    input.addEventListener("input", () => {
      let v = input.value.trim();
      if (v && !v.startsWith("bi-")) v = "bi-" + v;
      icon.className = "bi " + v;
    });
    input.addEventListener("blur", () => {
      const v = input.value.trim();
      if (v && !v.startsWith("bi-")) input.value = "bi-" + v;
    });
  });

  // Rich text editor for HTML fields (Quill), synced into the hidden textarea on submit
  if (window.Quill) {
    document.querySelectorAll(".rich-editor").forEach((el) => {
      const textarea = document.getElementById(el.dataset.target);
      const quill = new Quill(el, {
        theme: "snow",
        modules: {
          toolbar: [
            [{ header: [2, 3, false] }],
            ["bold", "italic", "underline", "link"],
            [{ list: "ordered" }, { list: "bullet" }],
            ["blockquote", "image", "clean"],
          ],
        },
      });
      quill.clipboard.dangerouslyPasteHTML(textarea.value);
      textarea.form.addEventListener("submit", () => {
        textarea.value = quill.getText().trim() === "" && !quill.root.querySelector("img") ? "" : quill.root.innerHTML;
      });
    });
  }
})();
