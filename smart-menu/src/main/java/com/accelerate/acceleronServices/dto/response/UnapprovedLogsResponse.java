package com.accelerate.acceleronServices.dto.response;

import java.util.ArrayList;
import java.util.List;

import com.accelerate.acceleronServices.enums.StatusCodes;

public class UnapprovedLogsResponse extends ApiResponse {

    private List<UnapprovedLogsResponseUnit> data = new ArrayList<>();

    public UnapprovedLogsResponse(boolean status, StatusCodes statusCode, String message) {
        super(status, statusCode, message);
    }

    public UnapprovedLogsResponse(boolean status, StatusCodes statusCode, String message,
            List<UnapprovedLogsResponseUnit> data) {
        super(status, statusCode, message);
        this.data = data;
    }

    public List<UnapprovedLogsResponseUnit> getData() {
        return data;
    }

    public void setData(List<UnapprovedLogsResponseUnit> data) {
        this.data = data;
    }

    public void addData(UnapprovedLogsResponseUnit data) {
        this.data.add(data);
    }
}
