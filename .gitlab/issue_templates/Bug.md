### 0. Tell the developer which branch to start from

- [ ] Tạo branch, merge request từ nhánh : ????
<!-- Tell the developer which branch to start from -->

### 1. Summary (Miêu tả bug)

<!-- Summarize the bug encountered concisely. -->

### 2. Original document (Tài liệu test, tài liệu feedback, có thể nhiều nguồn tài liệu)

<!-- Link to bug original document -->

### 3. Environment, version (Môi trường, phiên bản)

<!-- Describe environment used used for testing : Dev, Staging, etc ... -->

### 4. Prerequisite (Điều kiện để xảy ra bug)

<!-- Any prerequisite needed ... -->

### 5. Steps to reproduce (Các bước thực hiện)

<!-- Describe how one can reproduce the issue - this is very important. Please use an ordered list. -->

### 6. What is the current _bug_ behavior? (hiện tại đang xử lý _sai_ như thế nào?)

<!-- Describe what actually happens. -->

- [ ] Nếu **_làm gì_**, thì **_hệ thống xử lý sai thế nào_**

### 7. What is the expected _correct_ behavior? (nếu xử lý _đúng_ thì phải như thế nào? / Testcase)

<!-- Describe what you should see instead. -->

- [ ] Nếu **_làm gì_**, thì **_hệ thống cần xử lý thế nào_**
- [ ] Nếu **_làm gì_**, thì **_hệ thống cần xử lý thế nào_**

### 8. Relevant logs and/or screenshots (Logs hoặc screenshot)

<!-- Paste any relevant logs - please use code blocks (```) to format console output, logs, and code
 as it's tough to read otherwise. -->

### 9. Possible fixes (Đề xuất phương án sửa bug)

<!-- If you can, link to the line of code that might be responsible for the problem. -->
<!-- Nếu có thể chỉ ra source code nghi ngờ gây ra bug -->

### 10. Checklist (Các bước thực hiện)

- [ ] Điều tra, đề xuất phương án sửa bug (Mục 9)
- [ ] Viết testcase (manual-mục 7 hoặc UnitTest
- [ ] Code
- [ ] Test trên local
- [ ] Merge code vào nhánh development
- [ ] Test trên môi trường development
- [ ] Merge request Staging
- [ ] Update ● 　 vào file feedback sau khi merge lên staging

/label ~Bug
