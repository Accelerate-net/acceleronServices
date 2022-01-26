package com.accelerate.acceleronServices.repository;

import java.util.List;
import com.accelerate.acceleronServices.model.TeacherLog;

import org.springframework.stereotype.Repository;

@Repository
public interface TeacherLogRepository { //extends JpaRepository<TeacherLog, Long>

    public List<TeacherLog> findByTeacherCode(String teacherCode);
    
    public List<TeacherLog> findByIdIn(List<Long> ids);

    public List<TeacherLog> findByIsApprovedFalse();

    void save(TeacherLog teacherLog);
}
