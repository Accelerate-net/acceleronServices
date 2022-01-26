package com.accelerate.acceleronServices.dto.response;

import java.util.ArrayList;
import java.util.List;

import com.accelerate.acceleronServices.enums.StatusCodes;

public class TeacherLogResponse extends ApiResponse {

    private List<TeacherLogResponseUnit> data = new ArrayList<>();

    public TeacherLogResponse(boolean status, StatusCodes statusCode, String message) {
        super(status, statusCode, message);
    }

    public TeacherLogResponse(boolean status, StatusCodes statusCode, String message, List<TeacherLogResponseUnit> data) {
        super(status, statusCode, message);
        this.data = data;
    }

    public List<TeacherLogResponseUnit> getData() {
        return data;
    }

    public void setData(List<TeacherLogResponseUnit> data) {
        this.data = data;
    }

    public void addData(TeacherLogResponseUnit data) {
        this.data.add(data);
    }
}
