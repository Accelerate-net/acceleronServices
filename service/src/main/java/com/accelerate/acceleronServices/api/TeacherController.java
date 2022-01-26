package com.accelerate.acceleronServices.api;

import com.accelerate.acceleronServices.dto.request.TeacherLogRequest;
import com.accelerate.acceleronServices.dto.response.ApiResponse;
import com.accelerate.acceleronServices.dto.response.ErrorResponse;
import com.accelerate.acceleronServices.enums.StatusCodes;
import com.accelerate.acceleronServices.service.TeacherService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/teacher")
public class TeacherController {

    @Autowired
    private TeacherService teacherService;

    @PostMapping("/log")
    public ApiResponse logTime(@RequestBody TeacherLogRequest request, BindingResult bindingResult) {
        if (bindingResult.hasErrors()) return new ErrorResponse(false, StatusCodes.INPUT_VALIDATION_ERROR, "Invalid request");
        return teacherService.logTime(request);
    }

    @GetMapping("/log/{teacherCode}")
    public ApiResponse getLog(@PathVariable("teacherCode") String teacherCode) {
        return teacherService.getLog(teacherCode);
    }
}
