package com.accelerate.acceleronServices.service;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import com.accelerate.acceleronServices.dto.request.TeacherLogRequest;
import com.accelerate.acceleronServices.dto.response.*;
import com.accelerate.acceleronServices.enums.StatusCodes;
import com.accelerate.acceleronServices.model.TeacherLog;
import com.accelerate.acceleronServices.repository.TeacherLogRepository;
import com.accelerate.acceleronServices.utility.MiscUtilities;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

@Service
public class TeacherService {

    @Autowired
    private TeacherLogRepository teacherLogRepository;

    @Autowired
    private MiscUtilities utilities;

    public ApiResponse logTime(TeacherLogRequest request) {
        Long entryId = request.getEntryId();
        String today = utilities.getCurrentDatestamp();
        String teacherName = "Prof. Abhijith CS";
        TeacherLog teacherLog = new TeacherLog(1L,
                                                "dummy-teacher",
                                                teacherName, 
                                                request.getDate(),
                                                today, 
                                                request.getDuration(), 
                                                request.getTimeUnit(), 
                                                false);
        try {
            teacherLogRepository.save(teacherLog);
        } catch (Exception ex) {
            return new ErrorResponse(false, StatusCodes.INTERNAL_SERVER_ERROR, "Could not save log.");
        }

        return new SuccessResponse(true, StatusCodes.SUCCESS, "Progress logged successfully.");
    }

    public ApiResponse getLog(String teacherCode) {
        List<TeacherLog> logs = teacherLogRepository.findByTeacherCode(teacherCode);
        Map<Long, String> batchIds = new HashMap<>();
        Map<Long, String> moduleIds = new HashMap<>();
        TeacherLogResponse response = new TeacherLogResponse(true, StatusCodes.SUCCESS, "Fetched logs successfully.");
        for (TeacherLog log : logs) {
            Long mappingId = log.getMappingId();
            String moduleId;
            String batchId = batchIds.get(mappingId);
            moduleId = moduleIds.get(log.getMappingId());
            TeacherLogResponseUnit unit = new TeacherLogResponseUnit(batchId, moduleId, log.getDate(),
                    log.getDuration(), log.isApproved());
            response.addData(unit);
        }
        return response;
    }

}
