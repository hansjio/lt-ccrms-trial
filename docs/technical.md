# Technical Documentation

## Database Schema

### Persons Table (`lt_ccrms.persons`)
```sql
CREATE TABLE persons (
    person_id INT(11) PRIMARY KEY,
    first_name VARCHAR(50),
    middle_name VARCHAR(50),
    last_name VARCHAR(50),
    suffix VARCHAR(10)
);
```

### Cases Table (`lt_ccrms.cases`)
```sql
CREATE TABLE cases (
    case_no VARCHAR(10) PRIMARY KEY,
    title VARCHAR(255),
    nature ENUM('Criminal', 'Civil'),
    file_date DATE,
    confrontation_date DATE,
    action_taken VARCHAR(50),
    settlement_date VARCHAR(50),
    exec_settlement_date VARCHAR(50),
    main_agreement VARCHAR(255),
    compliance_status ENUM('Complete', 'Ongoing'),
    remarks VARCHAR(255),
    is_archived TINYINT(1)
);
```

### Case Persons Table (`lt_ccrms.case_persons`)
```sql
CREATE TABLE case_persons (
    case_no VARCHAR(10),
    person_id INT(11),
    role ENUM('Complainant', 'Respondent'),
    PRIMARY KEY (case_no, person_id),
    FOREIGN KEY (case_no) REFERENCES cases(case_no),
    FOREIGN KEY (person_id) REFERENCES persons(person_id)
);
```

## Table Relationships

1. **Persons and Cases** (Many-to-Many relationship through case_persons):
   - One person can be involved in multiple cases
   - One case can involve multiple persons
   - The `case_persons` table serves as a junction table with role specification

2. **Role Types**:
   - Each person in a case is assigned a role: either 'Complainant' or 'Respondent'

## Key Features of the Schema

### Case Management
- Unique case numbers using VARCHAR(10)
- Support for both Criminal and Civil cases
- Comprehensive tracking of important dates:
  - File date
  - Confrontation date
  - Settlement date
  - Executive settlement date
- Status tracking through compliance_status
- Archival capability through is_archived flag

### Person Information
- Complete name storage (first, middle, last, suffix)
- Unique person identification through person_id
- Flexible role assignment in cases

## API Endpoints

### Cases
```
GET /api/cases
Response:
{
    "cases": [
        {
            "case_no": "string",
            "title": "string",
            "nature": "Criminal|Civil",
            "file_date": "date",
            "compliance_status": "Complete|Ongoing"
        }
    ]
}

POST /api/cases
Request:
{
    "case_no": "string",
    "title": "string",
    "nature": "Criminal|Civil",
    "file_date": "date",
    "confrontation_date": "date",
    "action_taken": "string"
}
```

### Persons
```
GET /api/persons
Response:
{
    "persons": [
        {
            "person_id": "integer",
            "first_name": "string",
            "middle_name": "string",
            "last_name": "string",
            "suffix": "string"
        }
    ]
}

POST /api/persons
Request:
{
    "first_name": "string",
    "middle_name": "string",
    "last_name": "string",
    "suffix": "string"
}
```

### Case Persons
```
POST /api/case-persons
Request:
{
    "case_no": "string",
    "person_id": "integer",
    "role": "Complainant|Respondent"
}
```

## Security Implementation

### Input Validation
```php
function validateCaseNumber($case_no) {
    // Validate case number format
    if (!preg_match('/^[A-Z0-9-]{1,10}$/', $case_no)) {
        return false;
    }
    return true;
}

function validatePersonData($person) {
    // Sanitize and validate person data
    $person['first_name'] = trim(strip_tags($person['first_name']));
    $person['middle_name'] = trim(strip_tags($person['middle_name']));
    $person['last_name'] = trim(strip_tags($person['last_name']));
    $person['suffix'] = trim(strip_tags($person['suffix']));
    
    return $person;
}
```

## Query Examples

### Case Retrieval with Parties
```sql
SELECT 
    c.*,
    p.person_id,
    p.first_name,
    p.last_name,
    cp.role
FROM cases c
JOIN case_persons cp ON c.case_no = cp.case_no
JOIN persons p ON cp.person_id = p.person_id
WHERE c.case_no = ?
```

### Active Cases Summary
```sql
SELECT 
    c.case_no,
    c.title,
    c.nature,
    c.file_date,
    COUNT(DISTINCT CASE WHEN cp.role = 'Complainant' THEN cp.person_id END) as complainants,
    COUNT(DISTINCT CASE WHEN cp.role = 'Respondent' THEN cp.person_id END) as respondents
FROM cases c
LEFT JOIN case_persons cp ON c.case_no = cp.case_no
WHERE c.is_archived = 0
GROUP BY c.case_no
```

## Performance Optimization

### Recommended Indexes
```sql
-- Cases table indexes
CREATE INDEX idx_cases_compliance ON cases(compliance_status);
CREATE INDEX idx_cases_nature ON cases(nature);
CREATE INDEX idx_cases_file_date ON cases(file_date);
CREATE INDEX idx_cases_archived ON cases(is_archived);

-- Case persons indexes
CREATE INDEX idx_case_persons_role ON case_persons(role);
CREATE INDEX idx_case_persons_person ON case_persons(person_id);

-- Persons table indexes
CREATE INDEX idx_persons_name ON persons(last_name, first_name);
``` 