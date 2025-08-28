function loadPage(page = 1) {
  $.ajax({
    url: "ipc_handler_api.php",
    type: "post",
    contentType: "application/json",
    dataType: "json",
    data: JSON.stringify({ page: page }),
  })
  .done(function (result) {
    let content = "";

    result.data.forEach(item => {
      content += `
        <div class="card mb-3 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">${item.title}</h5>
            <p class="card-text">${item.description}</p>
      `;

      if (item.file_type === "image") {
        content += `<img src="${item.file_path}" class="img-fluid rounded">`;
      } else if (item.file_type === "pdf") {
        content += `
          <div class="ratio ratio-16x9">
            <iframe src="${item.file_path}" frameborder="0"></iframe>
          </div>
        `;
      }

      content += `
          </div>
        </div>
      `;
    });

    $("#content").html(content);
    renderPagination(result.totalPages, result.currentPage);
  });
}

function renderPagination(totalPages, currentPage) {
  let pagination = "";

  for (let i = 1; i <= totalPages; i++) {
    pagination += `
      <li class="page-item ${i === currentPage ? 'active' : ''}">
        <a class="page-link" href="#" onclick="loadPage(${i})">${i}</a>
      </li>
    `;
  }

  $("#pagination").html(pagination);
}

$(document).ready(function () {
  loadPage(1);
});
