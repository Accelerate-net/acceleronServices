package com.accelerate.acceleronServices.model;

import com.sun.istack.NotNull;

import javax.persistence.*;

@Entity
@Table(name = "teacher_log")
public class TeacherLog {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @NotNull
    private Long mappingId;

    private String teacherCode;

    private String teacherName;

    private String date;

    private String loggedDate;

    @NotNull
    private Double duration;

    @NotNull
    private String timeUnit;

    @NotNull
    private boolean isApproved;

    public TeacherLog() {}

    public TeacherLog(Long mappingId, String teacherCode, String teacherName, String date, 
            String loggedDate, Double duration, String timeUnit, boolean isApproved) {
        this.mappingId = mappingId;
        this.teacherCode = teacherCode;
        this.teacherName = teacherName;
        this.date = date;
        this.loggedDate = loggedDate;
        this.duration = duration;
        this.timeUnit = timeUnit;
        this.isApproved = isApproved;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public boolean isApproved() {
        return isApproved;
    }

    public void setApproved(boolean isApproved) {
        this.isApproved = isApproved;
    }

    public Long getMappingId() {
        return mappingId;
    }

    public void setMappingId(Long mappingId) {
        this.mappingId = mappingId;
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

    public String getLoggedDate() {
        return loggedDate;
    }

    public void setLoggedDate(String loggedDate) {
        this.loggedDate = loggedDate;
    }
}
