package com.accelerate.acceleronServices.utils;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.springframework.stereotype.Component;

@Component
public class CommonUtil {

    public String convertDateToDatestamp(String date) {
        Pattern regex = Pattern.compile("^(\\d\\d)-(\\d\\d)-(\\d\\d\\d\\d)$");
        Matcher matcher = regex.matcher(date);
        if (!matcher.find()) return null;
        date = matcher.group(3) + matcher.group(2) + matcher.group(1);
        return date;
    }

    public String convertDatestampToDate(String datestamp) {
        Pattern regex = Pattern.compile("^(\\d\\d\\d\\d)(\\d\\d)(\\d\\d)$");
        Matcher matcher = regex.matcher(datestamp);
        if (!matcher.find()) return null;
        datestamp = matcher.group(3) + "-" + matcher.group(2) + "-" + matcher.group(1);
        return datestamp;
    }

    public String getCurrentDatestamp() {
        DateFormat dateFormat = new SimpleDateFormat("yyyyMMdd");
        Date now = new Date();
        String date = dateFormat.format(now);

        return date;
    }

    public double bytesToMB(long bytes) {
        return ((double) bytes / (1024 * 1024));
    }

    public String getFileExtension(String fileName) {
        return fileName.substring(fileName.lastIndexOf('.') + 1);
    }
}
