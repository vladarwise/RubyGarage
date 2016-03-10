# RubyGarage
RubyGarage test project
## SQL task
### Given tables:
★ tasks (id, name, status, project_id)<br>
★ projects (id, name)
### Queries for:
1 get all statuses, not repeating, alphabetically ordered
```sql
SELECT DISTINCT status FROM tasks ORDER BY status;
```
2 get the count of all tasks in each project, order by tasks count descending
```sql 
SELECT projects.id, projects.name, COUNT(tasks.id) AS task_count FROM tasks RIGHT JOIN projects ON tasks.project_id = projects.id GROUP BY projects.id ORDER BY task_count DESC
```
3 get the count of all tasks in each project, order by projects names
```sql 
SELECT projects.id, projects.name, COUNT(tasks.id) AS task_count FROM tasks RIGHT JOIN projects ON tasks.project_id = projects.id GROUP BY projects.id ORDER BY projects.name
```
4 get the tasks for all projects having the name beginning with “N” letter
```sql 
SELECT tasks.*, projects.name as project_name FROM tasks RIGHT JOIN projects ON tasks.project_id = projects.id WHERE projects.name LIKE 'N%'
```
5 get the list of all projects containing the ‘a’ letter in the middle of the name, and show the tasks count near each project. Mention that there can exist projects without tasks and tasks with project_id=NULL
```sql
SELECT projects.name, COUNT(tasks.id) AS task_count FROM tasks RIGHT JOIN projects ON tasks.project_id = projects.id WHERE projects.name LIKE '%a%' GROUP BY projects.id ORDER BY projects.name
```
6 get the list of tasks with duplicate names. Order alphabetically
```sql
SELECT * FROM tasks WHERE name IN (SELECT name FROM tasks GROUP BY name HAVING COUNT(*)>1) ORDER BY name
```
7 get the list of tasks having several exact matches of both name and status, from the project ‘Garage’. Order by matches count
```
sql
```
8 get the list of project names having more than 10 tasks in status ‘completed’. Order by project_id
```sql
SELECT projects.name FROM tasks RIGHT JOIN projects ON tasks.project_id = projects.id WHERE tasks.status='completed' GROUP BY projects.id HAVING COUNT(tasks.id)>10 ORDER BY projects.name
```
