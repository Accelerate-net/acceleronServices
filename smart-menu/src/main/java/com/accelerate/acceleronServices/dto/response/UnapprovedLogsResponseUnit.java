package com.accelerate.acceleronServices.dto.response;

public class UnapprovedLogsResponseUnit {

    private Long entryId;

    private String batchId;

    private String moduleId;

    private String teacherCode;

    private String teacherName;

    private String date;

    private String loggedDate;

    private Double duration;

    private String timeUnit;

    public UnapprovedLogsResponseUnit() {}

    public UnapprovedLogsResponseUnit(Long entryId, String batchId, String moduleId, String teacherCode,
            String teacherName, String date, String loggedDate, Double duration, String timeUnit) {
        this.entryId = entryId;
        this.batchId = batchId;
        this.moduleId = moduleId;
        this.teacherCode = teacherCode;
        this.teacherName = teacherName;
        this.date = date;
        this.loggedDate = loggedDate;
        this.duration = duration;
        this.timeUnit = timeUnit;
    }

    public Long getEntryId() {
        return entryId;
    }

    public void setEntryId(Long entryId) {
        this.entryId = entryId;
    }

    public String getBatchId() {
        return batchId;
    }

    public void setBatchId(String batchId) {
        this.batchId = batchId;
    }

    public String getModuleId() {
        return moduleId;
    }

    public void setModuleId(String moduleId) {
        this.moduleId = moduleId;
    }

    public String getTeacherCode() {
        return teacherCode;
    }

    public void setTeacherCode(String teacherCode) {
        this.teacherCode = teacherCode;
    }

    public String getTeacherName() {
        return teacherName;
    }

    public void setTeacherName(String teacherName) {
        this.teacherName = teacherName;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getLoggedDate() {
        return loggedDate;
    }

    public void setLoggedDate(String loggedDate) {
        this.loggedDate = loggedDate;
    }

    public Double getDuration() {
        return duration;
    }

    public void setDuration(Double duration) {
        this.duration = duration;
    }

    public String getTimeUnit() {
        return timeUnit;
    }

    public void setTimeUnit(String timeUnit) {
        this.timeUnit = timeUnit;
    }
}
