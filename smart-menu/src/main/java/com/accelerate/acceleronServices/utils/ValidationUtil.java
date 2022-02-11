package com.accelerate.acceleronServices.utils;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;
import org.springframework.util.StringUtils;

@Component
public class ValidationUtil {

    @Value("${app.validation.dateFormat}")
    private String dateFormat;

    @Value("${app.validation.idFormat}")
    private String idFormat;

    private static final String EMAIL_FORMAT = "^([a-zA-Z0-9_\\-\\.]+)@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.)|(([a-zA-Z0-9\\-]+\\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\\]?)$";

    private static final String MOBILE_FORMAT = "^(\\d+)$";

    private static final String NAME_FORMAT = "^[a-zA-Z]+( [a-zA-Z]+)*$";

    private static final String PASSWORD_FORMAT = "^([a-zA-Z0-9_\\-\\.@]+)$";
    
    public boolean validateStudentID(String id) {
        idFormat = idFormat.replace("/", "\\");
        String regex = idFormat;
        Pattern pattern = Pattern.compile(regex);
        Matcher matcher = pattern.matcher(id);

        if (!matcher.find()) return false;
        return true;
    }

    public boolean validateDate(String date) {
        try {
            Date date1 = new SimpleDateFormat(dateFormat).parse(date);
        } catch (ParseException ex) {
            return false;
        } 
        return true;
    }

    public boolean validateName(String name) {
        String temp = StringUtils.trimAllWhitespace(name);
        if (temp.length() < 3) return false;

        Pattern pattern = Pattern.compile(NAME_FORMAT);
        Matcher matcher = pattern.matcher(name);

        if (!matcher.find()) return false;
        return true;
    }

    public boolean validateEmail(String email) {
        Pattern pattern = Pattern.compile(EMAIL_FORMAT);
        Matcher matcher = pattern.matcher(email);

        if (!matcher.find()) return false;
        return true;
    }

    public boolean validateMobile(String mobile) {
        if (mobile.length() != 10) return false;

        Pattern pattern = Pattern.compile(MOBILE_FORMAT);
        Matcher matcher = pattern.matcher(mobile);

        if (!matcher.find()) return false;
        return true;
    }

    public boolean validatePassword(String password) {
        if (StringUtils.trimAllWhitespace(password).equals("")) return false;
        if (password.length() < 6) return false;

        Pattern pattern = Pattern.compile(PASSWORD_FORMAT);
        Matcher matcher = pattern.matcher(password);

        if (!matcher.find()) return false;
        return true;
    }
}
