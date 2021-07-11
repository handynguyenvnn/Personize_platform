### 0. Tell the developer which branch to start from

- [ ] Tạo branch, merge request từ nhánh : ????
<!-- Tell the developer which branch to start from -->

## 1. Parent feature (Issue cha là Feature nào)

<!--
Implementation issues are used break-up a large piece of work into small, discrete tasks that can
move independently through the build workflow steps. They're typically used to populate a Feature
Epic. Once created, an implementation issue is usually refined in order to populate and review the
implementation plan and weight.
Example workflow: https://about.gitlab.com/handbook/engineering/development/threat-management/planning/diagram.html#plan
-->

## 2. Why are we doing this work (Tại sao cần phải implement cái này??)

<!--
A brief explanation of the why, not the what or how. Assume the reader doesn't know the
background and won't have time to dig-up information from comment threads.
-->

## 3. Relevant links (Các link liên quan --- tài liệu, gợi ý, etc,...)

## 4. Non-functional requirements (Các yêu cầu không phải tính năng)

<!--
Add details for required items and delete others.
-->

- [ ] Documentation (Viết tài liệu):
- [ ] Performance (Hiệu năng):
- [ ] Testing (test):

## 5. Implementation plan (Kế hoạch thực hiện)

<!--
Steps and the parts of the code that will need to get updated. The plan can also
call-out responsibilities for other team members or teams.
-->

## 6. Checklist (Các bước thực hiện)

- [ ] Điều tra, đề xuất phương án thực hiện (Mục 5)
- [ ] Viết testcase (manual hoặc UnitTest)
- [ ] Code
- [ ] Test trên local
- [ ] Merge code vào nhánh development
- [ ] Test trên môi trường development

<!--- chọn /label ~"UserStory::Implementation" hoặc "UserStory::Feature::Implementation" -->

/label ~"UserStory::Implementation"
