> # Test Bank

---

This project was created for the Guided Design in Software Engineering course at NJIT in the Summer 2022 semester. I wrote the backend portion; its contents are listed under `Front/Architecture/Release`.

The purpose of this project was to implement an online testing bank, similar to learning management systems (LMS) such as Canvas or Blackboard. This "test bank" was designed for both student and teacher access.

**Students** can log in to view and take available exams. Students can then view their grades for the exams they have taken, but only when teachers have released the exam grades.

## <u>Student view of graded exam</u>

![](/screenshots/Rubric.png)

**Teachers** can log in to add questions to the test bank, create exams that consist of said questions and view any exams that a student has submitted for grading. 

## <u>Teacher view of exam creation</u>

![](/screenshots/Exam%20Creation.png)

Exams consist of basic programming questions that must be given solutions written in Python. Questions that are stored in the test bank are specified by a function call, some number of test cases, a category and an optional constraint. Since a teacher may want to reuse certain questions on an exam (to reinforce some topic, for example), existing questions from the test bank are shown. The type of question that a teacher may want can also be filtered in multiple ways. An exam question will be automatically graded based on whether the student's response satisifies the mentioned criteria. However, teachers may view the auto-graded result and overwrite point values.

## <u>Teacher view of question creation</u>

![](/screenshots/Question%20Creation.png)

## <u>Student view of an exam question</u>

![](/screenshots/Exam%20Question.png)

This project was required to comply with a typical three-tier architecture in software applications (model, view and controller). Backend functionality was implemented using PHP (mainly to parse incoming JSON data) and MySQLi (database driver). Database management and design was implemented using phpMyAdmin.

# [The full project can be viewed here (redirects to Heroku deploy).](https://testbank-main.herokuapp.com/)